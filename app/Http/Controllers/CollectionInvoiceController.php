<?php

namespace App\Http\Controllers;

use App\Models\JebayaInvoice;
use Illuminate\Http\Request;

/**
 * CollectionInvoiceController
 *
 * شاشة الاستعلام عن فواتير الجباية ضمن خانة "قلم الجمهور".
 * يستعلم من جدول jebaya_invoices المستورد من ملف fawatweeer.xlsx
 * حسب الاسم / رقم الجوال / رقم السند / رقم الاشتراك.
 */
class CollectionInvoiceController extends Controller
{
    const PER_PAGE = 15;

    /**
     * عرض شاشة الاستعلام عن فواتير الجباية.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $invoices = collect();
        $totals = [
            'count' => 0, 'net' => 0.0, 'late' => 0.0, 'amt' => 0.0,
        ];

        if ($search !== '') {
            $invoices = $this->runQuery($search);
            $totals = $this->computeTotals($search);
        }

        return view('mokalafs.jebaya_inquiry', [
            'search'   => $search,
            'invoices' => $invoices,
            'totals'   => $totals,
        ]);
    }

    /**
     * عرض نسخة طباعة من نتائج الاستعلام.
     */
    public function print(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        if ($search === '') {
            return redirect()->route('collection.inquiry')
                ->with('warning', 'يجب إدخال مصطلح بحث للطباعة.');
        }

        $invoices = $this->runQuery($search, 500);

        return view('mokalafs.jebaya_inquiry_print', [
            'search'   => $search,
            'invoices' => $invoices->getCollection(),
            'totals'   => $this->computeTotals($search),
        ]);
    }

    /**
     * عرض تفاصيل فاتورة جباية واحدة للطباعة.
     */
    public function show($id)
    {
        $invoice = JebayaInvoice::findOrFail($id);

        return view('mokalafs.jebaya_invoice_print', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * تنفيذ الاستعلام عن فواتير الجباية باستخدام نطاق البحث المرن.
     *
     * عند البحث بالاسم تُرتَّب النتائج حسب مدى التزامها بنطاق البحث: الأسماء
     * التي تبدأ بالعبارة المطلوبة أولاً ثم الأقرب إليها، ثم تاريخ السند.
     */
    protected function runQuery(string $search, int $perPage = self::PER_PAGE)
    {
        $term = trim($search);
        $query = JebayaInvoice::search($term);

        $normalized = normalizeArabicText($term);
        if (preg_match('/\p{Arabic}/u', $normalized)) {
            $nameExpr = JebayaInvoice::normalizedNameExpression();
            // 1) العبارة الكاملة أولاً (تطابق متجاور)، ثم 2) تطابق كل الكلمات بأي ترتيب، ثم الأحدث
            $phraseLike = '%' . escapeLike($normalized) . '%';
            $query->orderByRaw("CASE WHEN {$nameExpr} LIKE ? THEN 0 ELSE 1 END", [$phraseLike]);
            // موضع العبارة داخل الاسم: الأعلى تطابقاً (الموضع الأصغر) يأتي أولاً
            // (LOCATE=0 لغير المطابقين للعبارة يأتي قبل المواضع — نعكسها حتى لا تتقدم نتائج الـ fallback)
            $query->orderByRaw("CASE WHEN LOCATE(?, {$nameExpr}) = 0 THEN 999999 ELSE LOCATE(?, {$nameExpr}) END", [$normalized, $normalized]);
        }

        return $query
            ->orderByDesc('voucher_date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * حساب مجاميع النتائج بنفس شروط البحث المرن.
     */
    protected function computeTotals(string $search): array
    {
        $agg = JebayaInvoice::search($search)
            ->selectRaw('COUNT(*) AS c')
            ->selectRaw('COALESCE(SUM(net_amt),0) AS s_net')
            ->selectRaw('COALESCE(SUM(total_late),0) AS s_late')
            ->selectRaw('COALESCE(SUM(voucher_amt),0) AS s_amt')
            ->first();

        return [
            'count' => (int) ($agg->c ?? 0),
            'net'   => (float) ($agg->s_net ?? 0),
            'late'  => (float) ($agg->s_late ?? 0),
            'amt'   => (float) ($agg->s_amt ?? 0),
        ];
    }
}

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>طباعة الاستعلام عن فواتير الجباية</title>
    <link href="{{ asset('assets/css/pages/print.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-maroon: #b03a2e;
            --brand-maroon-dark: #8a2d23;
            --brand-gold: #c9962e;
            --brand-gold-light: #f4e6c8;
            --ink: #1a1a1a;
            --muted: #6b6b6b;
            --row-alt: #faf6ef;
            --border: #e2d9c8;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Tajawal', 'Cairo', 'Arial', sans-serif;
            color: var(--ink);
            margin: 0;
            padding: 24px;
            background: #fff;
        }

        /* رأس الصفحة */
        .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--brand-maroon);
            padding-bottom: 12px;
            margin-bottom: 4px;
        }
        .report-header-titles { text-align: right; }
        .muni-ar {
            font-weight: 800;
            font-size: 18px;
            color: var(--brand-maroon);
            margin: 0;
        }
        .muni-sub {
            font-size: 11px;
            color: var(--muted);
            margin: 2px 0 0;
        }
        .report-header-badge {
            background: linear-gradient(135deg, var(--brand-gold), var(--brand-maroon));
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .report-title-bar {
            text-align: center;
            background: linear-gradient(135deg, var(--brand-maroon), var(--brand-maroon-dark));
            color: #fff;
            font-weight: 800;
            font-size: 17px;
            padding: 10px 0;
            border-radius: 8px;
            margin: 16px 0 6px;
        }

        .report-meta {
            text-align: center;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 16px;
        }
        .report-meta span {
            display: inline-block;
            margin: 0 6px;
        }
        .report-meta strong {
            color: var(--ink);
            font-weight: 700;
        }

        /* الجدول */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 12px;
        }
        thead th {
            background: var(--brand-maroon);
            color: #fff;
            font-weight: 700;
            padding: 9px 6px;
            text-align: center;
            border: 1px solid var(--brand-maroon-dark);
        }
        tbody td {
            border: 1px solid var(--border);
            padding: 7px 6px;
            text-align: center;
            vertical-align: middle;
        }
        tbody tr:nth-child(even) {
            background: var(--row-alt);
        }
        tbody tr:hover {
            background: var(--brand-gold-light);
        }

        .totals-row td {
            font-weight: 800;
            background: var(--brand-gold-light);
            border-top: 2px solid var(--brand-gold);
            color: var(--brand-maroon-dark);
        }
        .totals-label {
            text-align: left !important;
            padding-left: 12px !important;
        }

        .empty-state {
            text-align: center;
            padding: 30px 0;
            color: var(--muted);
            font-size: 13px;
        }

        .report-footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid var(--border);
            font-size: 10px;
            color: var(--muted);
            display: flex;
            justify-content: space-between;
        }

        @media print {
            body { padding: 0; }
            @page { size: A4; margin: 12mm; }
            thead { display: table-header-group; } /* تكرار الترويسة في كل صفحة */
            tr { page-break-inside: avoid; }
            .report-header-badge { display: none; }
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="report-header-titles">
            <p class="muni-ar">بلدية جباليا النزلة</p>
            <p class="muni-sub">دائرة الجباية والتحصيل</p>
        </div>
        <div class="report-header-badge">تقرير رسمي</div>
    </div>

    <div class="report-title-bar">الاستعلام عن فواتير الجباية</div>

    <div class="report-meta">
        <span>تاريخ الطباعة: <strong>{{ now()->format('Y-m-d H:i') }}</strong></span>
        <span>|</span>
        <span>نص البحث: <strong>{{ $search ?: '—' }}</strong></span>
        <span>|</span>
        <span>عدد السجلات: <strong>{{ count($invoices) }}</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th>الاسم</th>
                <th>رقم الاشتراك</th>
                <th>رقم السند</th>
                <th>التاريخ</th>
                <th>الشهر</th>
                <th>المستحق</th>
                <th>الصافي</th>
                <th>المتأخرات</th>
                <th>الحسم</th>
                <th>الجوال</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoices as $inv)
                <tr>
                    <td>{{ $inv->arabic_name }}</td>
                    <td>{{ $inv->agreement_id ?? '-' }}</td>
                    <td>{{ $inv->voucher_no ?? '-' }}</td>
                    <td>{{ optional($inv->voucher_date)->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $inv->voucher_month ?? '-' }}</td>
                    <td>{{ number_format($inv->voucher_amt ?? 0, 2) }}</td>
                    <td>{{ number_format($inv->net_amt ?? 0, 2) }}</td>
                    <td>{{ number_format($inv->total_late ?? 0, 2) }}</td>
                    <td>{{ number_format($inv->auto_discount_ratio ?? 0, 2) }}</td>
                    <td>{{ $inv->mobile_no ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="empty-state">لا توجد فواتير مطابقة لهذا البحث</td>
                </tr>
            @endforelse

            @if (count($invoices))
                <tr class="totals-row">
                    <td colspan="5" class="totals-label">المجموع</td>
                    <td>{{ number_format($totals['amt'] ?? 0, 2) }}</td>
                    <td>{{ number_format($totals['net'] ?? 0, 2) }}</td>
                    <td>{{ number_format($totals['late'] ?? 0, 2) }}</td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="report-footer">
        <span>نظام إدارة الوثائق البلدية — بلدية جباليا النزلة</span>
        <span>صفحة {{ '1' }}</span>
    </div>

    <script>
        // تأخير بسيط لضمان اكتمال رسم الجدول وتطبيق التنسيقات قبل استدعاء الطباعة
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 300);
        });
    </script>
</body>
</html>
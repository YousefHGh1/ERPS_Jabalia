<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMokalafRequest;
use App\Http\Requests\UpdateMokalafRequest;
use App\Models\Mokalaf;
use App\Models\Request as RequestModel;
use App\Models\RequestType;
use App\Models\CraftLicense;
use App\Models\Engineering;
use App\Models\ImprisonmentOrder;
use App\Models\Reservation;
use App\Models\Notice;
use App\Models\ExternalCollection;
use App\Services\MokalafService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MokalafController extends Controller
{
    private $citizenController;
    private MokalafService $mokalafService;

    public function __construct(CitizenController $citizenController, MokalafService $mokalafService)
    {
        $this->citizenController = $citizenController;
        $this->mokalafService = $mokalafService;
        $this->authorizeResource(Mokalaf::class, 'mokalaf');
    }

    /**
     * عرض المكلفين الذين قاموا بعمل طلب ترخيص بناء — للهندسة والتنظيم
     */
    public function indexForEngineering(Request $request)
    {
        $this->authorize('viewAny', Mokalaf::class);

        $requestType = RequestType::where('name', 'like', '%ترخيص بناء%')
            ->orWhere('name', 'like', '%ترخيص البناء%')
            ->first();

        $mokalafs = $this->mokalafsByRequestType($requestType, $request);
        $pageTitle = __('المكلفين — طلب ترخيص بناء');
        $pageSubtitle = __('عرض المكلفين الذين قاموا بتقديم طلب ترخيص بناء للهندسة والتنظيم');
        $emptyMessage = __('لا يوجد مكلفون قدموا طلب ترخيص بناء');

        return view('mokalafs.by-request-type', compact('mokalafs', 'pageTitle', 'pageSubtitle', 'emptyMessage'));
    }

    /**
     * عرض المكلفين الذين قاموا بعمل طلب حرفة — للحرف والصناعات
     */
    public function indexForCrafts(Request $request)
    {
        $this->authorize('viewAny', Mokalaf::class);

        $requestType = RequestType::where('name', 'like', '%حرفة%')
            ->orWhere('name', 'like', '%طلب حرفة%')
            ->first();

        $mokalafs = $this->mokalafsByRequestType($requestType, $request);
        $pageTitle = __('المكلفين — طلب حرفة');
        $pageSubtitle = __('عرض المكلفين الذين قاموا بتقديم طلب حرفة للحرف والصناعات');
        $emptyMessage = __('لا يوجد مكلفون قدموا طلب حرفة');

        return view('mokalafs.by-request-type', compact('mokalafs', 'pageTitle', 'pageSubtitle', 'emptyMessage'));
    }

    /**
     * استعلام المكلفين حسب نوع الطلب مع بحث وترقيم - محسن
     */
    private function mokalafsByRequestType(?RequestType $requestType, Request $httpRequest)
    {
        $query = Mokalaf::query();

        if ($requestType) {
            $query->whereHas('requests', function ($q) use ($requestType) {
                $q->where('request_type_id', $requestType->id);
            });
        } else {
            $query->whereRaw('1 = 0');
        }

        $search = $httpRequest->input('search');
        $identityNumber = $httpRequest->input('identity_number');
        $address = $httpRequest->input('address');

        // Use parameterized queries for better performance
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($q2) use ($search) {
                $q2->where('name', 'like', '%' . $search . '%')
                   ->orWhere('address', 'like', '%' . $search . '%');
            });
        })
        ->when($identityNumber, function ($q) use ($identityNumber) {
            $q->where(function ($q2) use ($identityNumber) {
                $q2->where('identity_number', 'like', '%' . $identityNumber . '%')
                   ->orWhere('license_number', 'like', '%' . $identityNumber . '%');
            });
        })
        ->when($address, function ($q) use ($address) {
            $q->where('address', 'like', '%' . $address . '%');
        })
        ->select(['id', 'name', 'identity_number', 'license_number', 'address', 'mobile', 'created_at']) // Only select needed columns
        ->orderBy('id', 'desc');

        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
        $paginator = $query->paginate(10);
        return $paginator->withQueryString();
    }

    /**
     * صفحة ملف المكلف العامة - تعرض كل السجلات المرتبطة برقم الهوية.
     * استخدام eager loading للطلبات وتجميع استعلامات الربط بالهوية في دالة واحدة.
     */
    public function show(Mokalaf $mokalaf)
    {
        $mokalaf->load([
            'requests' => fn ($q) => $q->with(['status', 'requestType'])->latest(),
        ]);
        $requests = $mokalaf->requests;

        $related = $this->getRelatedRecordsForShow($mokalaf);
        $craftLicenses = $related['craftLicenses'] ?? collect();
        $engineerings = $related['engineerings'] ?? collect();
        $externalCollections = $related['externalCollections'] ?? collect();

        $sectionRemainingTotals = [
            'requests' => (float) $requests->sum(fn ($item) => (float) ($item->remaining_amount ?? 0)),
            'craft_licenses' => (float) $craftLicenses->sum(fn ($item) => (float) ($item->remaining ?? 0)),
            'engineerings' => (float) $engineerings->sum(fn ($item) => (float) ($item->remaining_amount ?? 0)),
            'external_collections' => (float) $externalCollections->sum(fn ($item) => (float) ($item->remaining_amount ?? 0)),
        ];
        $overallRemainingTotal = array_sum($sectionRemainingTotals);

        return view('mokalafs.show', array_merge(
            compact('mokalaf', 'requests', 'sectionRemainingTotals', 'overallRemainingTotal'),
            $related
        ));
    }

    /**
     * جلب السجلات المرتبطة بالمكلف برقم الهوية (تراخيص، هندسة، أوامر حبس، حجوزات، إخطارات) - محسن
     */
    private function getRelatedRecordsForShow(Mokalaf $mokalaf): array
    {
        $identity = $mokalaf->identity_number;
        $license = $mokalaf->license_number;

        // Use eager loading and limit columns for better performance
        $craftLicenses = collect();
        if ($identity || $license) {
            $craftLicenses = CraftLicense::query()
                ->select(['id', 'file_number', 'id_number', 'owner_name', 'commercial_name', 'total_debts', 'payment_amount', 'remaining', 'status', 'created_at'])
                ->where(function ($q) use ($identity, $license) {
                    if ($identity) {
                        $q->orWhere('id_number', $identity);
                    }
                    if ($license) {
                        $q->orWhere('id_number', $license)
                            ->orWhere('file_number', $license);
                    }
                })
                ->latest()
                ->limit(50) // Reduced from 200 for better performance
                ->get();
        }

        $engineerings = $identity
            ? Engineering::select(['id', 'file_number', 'first_name', 'father_name', 'grandfather_name', 'family_name', 'national_id', 'total_amount', 'paid_amount', 'remaining_amount', 'created_at'])
                ->where('national_id', $identity)
                ->latest()
                ->limit(50) // Reduced from 200
                ->get()
            : collect();

        $imprisonmentOrders = $identity
            ? ImprisonmentOrder::select(['id', 'order_number', 'order_date', 'defendant_identity', 'defendant_name', 'status', 'created_at'])
                ->where('defendant_identity', $identity)
                ->latest()
                ->limit(50) // Reduced from 200
                ->get()
            : collect();

        $reservations = $identity
            ? Reservation::query()
                ->select(['id', 'reservation_no', 'reservation_number', 'reservation_date', 'subject', 'description', 'status', 'created_at'])
                ->with(['defendant:id,name,identity_number', 'summoner:id,name,identity_number'])
                ->whereHas('defendant', fn ($q2) => $q2->where('identity_number', $identity))
                  ->orWhereHas('summoner', fn ($q2) => $q2->where('identity_number', $identity))
                ->latest()
                ->limit(50) // Reduced from 200
                ->get()
            : collect();

        $notices = $identity
            ? Notice::select(['id', 'notice_number', 'identity_number', 'notice_date', 'status', 'created_at'])
                ->where('identity_number', $identity)
                ->latest()
                ->limit(50) // Reduced from 200
                ->get()
            : collect();

        $externalCollections = $identity
            ? ExternalCollection::select(['id', 'taxpayer_name', 'identity_number', 'section_id', 'amount', 'paid_amount', 'remaining_amount', 'payment_status', 'created_at'])
                ->with('section:id,name')
                ->where('identity_number', $identity)
                ->orWhere('mokalaf_id', $mokalaf->id)
                ->latest()
                ->limit(50)
                ->get()
            : collect();

        return compact('craftLicenses', 'engineerings', 'imprisonmentOrders', 'reservations', 'notices', 'externalCollections');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $identityNumber = $request->input('identity_number');
        $address = $request->input('address');

        $mokalafs = Mokalaf::query()
            ->select(['id', 'name', 'identity_number', 'license_number', 'address', 'mobile', 'created_at']) // Only select needed columns
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('address', 'like', '%' . $search . '%');
            })
            ->when($identityNumber, function ($query) use ($identityNumber) {
                $query->where('identity_number', 'like', '%' . $identityNumber . '%')
                      ->orWhere('license_number', 'like', '%' . $identityNumber . '%');
            })
            ->when($address, function ($query) use ($address) {
                $query->where('address', 'like', '%' . $address . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('mokalafs.index', compact('mokalafs'));
    }

    public function create()
    {
        if (request()->has('modal') && request()->get('modal') == '1') {
            // Return form partial for modal
            return view('mokalafs._form');
        }
        // تمرير قيم من query string (مثل الانتقال من صفحة عرض مكلف) لتعبئة النموذج
        $identity_number = request('identity_number', '');
        $license_number = request('license_number', '');
        $name = request('name', '');
        $address = request('address', '');
        $mobile = request('mobile', '');
        return view('mokalafs.create', compact('identity_number', 'license_number', 'name', 'address', 'mobile'));
    }

    public function store(StoreMokalafRequest $request)
    {
        try {
            // Check if mokalaf already exists (باستخدام الخدمة لسهولة إعادة الاستخدام)
            $existingMokalaf = $this->mokalafService->findExisting(
                $request->identity_number,
                $request->license_number
            );

            if ($existingMokalaf) {
                $identifier = $request->filled('identity_number') ? 'رقم الهوية' : 'رقم الترخيص';
                $identifierValue = $request->filled('identity_number') ? $request->identity_number : $request->license_number;
                
                if ($request->has('from_modal') && $request->from_modal == '1') {
                    return response()->json([
                        'success' => false,
                        'message' => 'المكلف مضاف مسبقاً',
                        'error_type' => 'duplicate',
                        'details' => "المكلف برقم ({$identifierValue}) مضاف مسبقاً في النظام",
                        'edit_url' => route('mokalafs.edit', $existingMokalaf),
                        'mokalaf_id' => $existingMokalaf->id,
                        'mokalaf_name' => $existingMokalaf->name
                    ], 422);
                }
                return redirect()->back()
                    ->withInput()
                    ->with('error', "المكلف برقم ({$identifierValue}) مضاف مسبقاً في النظام");
            }

            // Create mokalaf
            $mokalaf = Mokalaf::create([
                'name' => $request->name,
                'identity_number' => $request->identity_number,
                'license_number' => $request->license_number,
                'address' => $request->address,
                'mobile' => $request->mobile,
                'created_by' => Auth::id(),
            ]);

            // Log activity
            activity()
                ->performedOn($mokalaf)
                ->causedBy(Auth::user())
                ->log('تم إضافة مكلف: ' . $mokalaf->name);

            // Check if request came from modal
            if ($request->has('from_modal') && $request->from_modal == '1') {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إضافة المكلف بنجاح'
                ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
            }

            return redirect()->route('mokalafs.index')
                ->with('success', 'تم إضافة المكلف بنجاح');

        } catch (\Exception $e) {
            if ($request->has('from_modal') && $request->from_modal == '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إضافة المكلف: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة المكلف: ' . $e->getMessage());
        }
    }

    public function edit(Mokalaf $mokalaf)
    {
        if (request()->has('modal') && request()->get('modal') == '1') {
            // Return form partial for modal
            return view('mokalafs._edit_form', compact('mokalaf'));
        }
        return view('mokalafs.edit', compact('mokalaf'));
    }

    public function update(UpdateMokalafRequest $request, Mokalaf $mokalaf)
    {
        try {
            $mokalaf->update([
                'name' => $request->name,
                'address' => $request->address,
                'mobile' => $request->mobile,
                'updated_by' => Auth::id(),
            ]);

            // Log activity
            activity()
                ->performedOn($mokalaf)
                ->causedBy(Auth::user())
                ->log('تم تعديل بيانات المكلف: ' . $mokalaf->name);

            // Check if request came from modal
            if ($request->has('from_modal') && $request->from_modal == '1') {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث بيانات المكلف بنجاح'
                ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
            }

            return redirect()->route('mokalafs.index')
                ->with('success', 'تم تحديث بيانات المكلف بنجاح');

        } catch (\Exception $e) {
            if ($request->has('from_modal') && $request->from_modal == '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء تحديث بيانات المكلف: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث بيانات المكلف: ' . $e->getMessage());
        }
    }

    public function destroy(Mokalaf $mokalaf)
    {
        try {
            // Log activity before delete
            activity()
                ->performedOn($mokalaf)
                ->causedBy(Auth::user())
                ->log('تم حذف المكلف: ' . $mokalaf->name);
            
            $mokalaf->delete();
            return redirect()->route('mokalafs.index')
                ->with('success', 'تم حذف المكلف بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('mokalafs.index')
                ->with('error', 'حدث خطأ أثناء حذف المكلف: ' . $e->getMessage());
        }
    }

    public function getCitizenData(Request $request)
    {
        $identityNumber = $request->get('identity_number');
        if (!$identityNumber) {
            return response()->json(['error' => 'رقم الهوية مطلوب'], 422);
        }

        try {
            $mappedData = $this->mokalafService->fetchCitizenData(
                $identityNumber,
                $this->citizenController,
                $request
            );

            if (empty($mappedData)) {
                return response()->json(['error' => 'لم يتم العثور على بيانات المواطن'], 404);
            }

            $hasName = !empty($mappedData['FNAME_ARB'] ?? null)
                || !empty($mappedData['SNAME_ARB'] ?? null)
                || !empty($mappedData['TNAME_ARB'] ?? null)
                || !empty($mappedData['LNAME_ARB'] ?? null);

            if (! $hasName) {
                return response()->json(['error' => 'لم يتم العثور على بيانات الاسم للمواطن'], 404);
            }

            return response()->json($mappedData);
        } catch (\Exception $e) {
            Log::error('Mokalaf getCitizenData error: ' . $e->getMessage(), [
                'identity_number' => $identityNumber,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }

    public function checkMokalafExists(Request $request)
    {
        $identityNumber = $request->get('identity_number');
        $licenseNumber = $request->get('license_number');

        $mokalaf = null;
        if ($identityNumber) {
            $mokalaf = Mokalaf::where('identity_number', $identityNumber)->first();
        } elseif ($licenseNumber) {
            $mokalaf = Mokalaf::where('license_number', $licenseNumber)->first();
        }

        if ($mokalaf) {
            return response()->json([
                'exists' => true,
                'mokalaf_id' => $mokalaf->id,
                'edit_url' => route('mokalafs.edit', $mokalaf)
            ]);
        }

        return response()->json(['exists' => false]);
    }
}

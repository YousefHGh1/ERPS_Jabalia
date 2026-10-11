<?php

namespace App\Http\Controllers\Notices;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CitizenController;
use App\Http\Requests\StoreNoticeRequest;
use App\Http\Requests\UpdateNoticeRequest;
use App\Models\Mokalaf;
use App\Models\Notice;
use App\Models\NoticeReason;
use App\Models\RentLocation;
use App\Models\NoticeAttachment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class NoticeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Notice::class, 'notice');
    }

    protected function splitFullName(?string $name): array
    {
        $name = trim((string) $name);
        if ($name === '') return ['', '', '', ''];
        $parts = preg_split('/\s+/', $name);
        $first = $parts[0] ?? '';
        $second = $parts[1] ?? '';
        $third = $parts[2] ?? '';
        if (count($parts) <= 3) {
            $last = $parts[3] ?? ($parts[2] ?? ($parts[1] ?? $parts[0] ?? ''));
        } else {
            $last = implode(' ', array_slice($parts, 3));
        }
        return [$first, $second, $third, $last];
    }

    public function fetchId(Request $request)
    {
        $q = $request->get('q', $request->get('term', ''));
        if (strlen($q) < 2) return response()->json([]);
        $items = Mokalaf::where('identity_number', 'like', '%' . $q . '%')
            ->limit(20)->get(['identity_number'])->unique('identity_number')->values()
            ->map(fn ($m) => ['identity_number' => $m->identity_number]);
        return response()->json($items);
    }

    public function fetchName(Request $request)
    {
        $q = $request->get('q', $request->get('term', ''));
        if (strlen($q) < 2) return response()->json([]);
        $items = Mokalaf::where('name', 'like', '%' . $q . '%')
            ->limit(20)->get(['id', 'name'])->map(fn ($m) => ['id' => $m->id, 'name' => $m->name]);
        return response()->json($items);
    }

    public function fetchJebaia(Request $request)
    {
        return response()->json([]);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $noticeReasonId = $request->input('notice_reason_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $notices = Notice::with(['noticeReason', 'rentLocation', 'creator'])
            ->when($search, fn ($q) => $q->where('notice_number', 'like', '%' . $search . '%')->orWhere('description', 'like', '%' . $search . '%'))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($noticeReasonId, fn ($q) => $q->where('notice_reason_id', $noticeReasonId))
            ->when($fromDate && $toDate, fn ($q) => $q->whereBetween('notice_date', [$fromDate, $toDate]))
            ->orderBy('id', 'desc')->paginate(10);

        $noticeReasons = NoticeReason::select('id', 'name')->orderBy('name')->get();
        $rentLocations = RentLocation::select('id', 'name')->orderBy('name')->get();
        return view('notices.index', compact('notices', 'noticeReasons', 'rentLocations'));
    }

    public function create()
    {
        $query = request()->only(['identity_number', 'name', 'address', 'mobile']);
        return redirect()->route('notices.index', $query)->with('openAddModal', true);
    }

    public function store(StoreNoticeRequest $request)
    {
        if ($request->has('year') && $request->has('notice_id')) {
            return $this->storeAudiencePenFormat($request);
        }

        try {
            DB::beginTransaction();
            $noticeData = [
                'notice_number' => $request->notice_number,
                'identity_number' => $request->input('identity_number'),
                'notice_date' => $request->notice_date,
                'notice_reason_id' => $request->notice_reason_id,
                'rent_location_id' => $request->rent_location_id,
                'description' => $request->description,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ];
            $notice = Notice::create($noticeData);
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('notice_attachments', 'public');
                    NoticeAttachment::create([
                        'notice_id' => $notice->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'created_by' => Auth::id(),
                    ]);
                }
            }
            DB::commit();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم إضافة الإخطار بنجاح']);
            }
            return redirect()->route('notices.index')->with('success', 'تم إضافة الإخطار بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء إضافة الإخطار: ' . $e->getMessage()], 422);
            }
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إضافة الإخطار: ' . $e->getMessage());
        }
    }

    protected function storeAudiencePenFormat(Request $request)
    {
        try {
            DB::beginTransaction();
            $noticeNumber = 'N' . $request->year . '-' . $request->notice_id;
            if (Notice::where('notice_number', $noticeNumber)->exists()
                || Notice::where('year', $request->year)->where('notice_id', $request->notice_id)->exists()) {
                throw ValidationException::withMessages(['notice_id' => ['سنة/رقم الإخطار مستخدم مسبقاً.']]);
            }

            $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->notice_date);
            Notice::create([
                'year' => $request->year,
                'notice_id' => $request->notice_id,
                'notice_number' => $noticeNumber,
                'identity_number' => $request->input('identity_number'),
                'notice_date' => $dateTime->toDateString(),
                'payment_date' => $dateTime,
                'notice_reason_id' => $request->notice_reason_id,
                'rent_location_id' => $request->rent_location_id,
                'jebaia_no' => $request->jebaia_no,
                'fine_amount' => $request->fine_amount,
                'notice_address' => $request->notice_address,
                'notice_month' => $request->notice_month,
                'org_file_number' => $request->org_file_number,
                'rent_no' => $request->rent_no,
                'build_desc' => $request->build_desc,
                'status' => 'draft',
                'created_by' => Auth::id(),
                'user_id' => $request->user_id,
            ]);

            DB::commit();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم إضافة الإخطار بنجاح']);
            }
            return redirect()->route('notices.index')->with('success', 'تم إضافة الإخطار بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء إضافة الإخطار: ' . $e->getMessage(), 'errors' => ['exception' => [$e->getMessage()]]], 422);
            }
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إضافة الإخطار: ' . $e->getMessage());
        }
    }

    public function show(Notice $notice)
    {
        return redirect()->route('notices.edit', $notice);
    }

    public function edit(Notice $notice)
    {
        $notice->load(['attachments']);
        $noticeReasons = NoticeReason::select('id', 'name')->orderBy('name')->get();
        $rentLocations = RentLocation::select('id', 'name')->orderBy('name')->get();
        if (request()->has('modal') && request()->get('modal') == '1') {
            return view('notices._edit_form', compact('notice', 'noticeReasons', 'rentLocations'));
        }
        return view('notices.edit', compact('notice', 'noticeReasons', 'rentLocations'));
    }

    public function update(UpdateNoticeRequest $request, Notice $notice)
    {
        try {
            DB::beginTransaction();
            $notice->update([
                'notice_number' => $request->notice_number,
                'notice_date' => $request->notice_date,
                'notice_reason_id' => $request->notice_reason_id,
                'rent_location_id' => $request->rent_location_id,
                'description' => $request->description,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('notice_attachments', 'public');
                    NoticeAttachment::create([
                        'notice_id' => $notice->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'created_by' => Auth::id(),
                    ]);
                }
            }
            DB::commit();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم تحديث الإخطار بنجاح']);
            }
            return redirect()->route('notices.index')->with('success', 'تم تحديث الإخطار بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تحديث الإخطار: ' . $e->getMessage(), 'errors' => ['exception' => [$e->getMessage()]]], 422);
            }
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الإخطار: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Notice $notice)
    {
        try {
            $notice->delete();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم حذف الإخطار بنجاح']);
            }
            return redirect()->route('notices.index')->with('success', 'تم حذف الإخطار بنجاح');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء حذف الإخطار: ' . $e->getMessage(), 'errors' => ['exception' => [$e->getMessage()]]], 422);
            }
            return redirect()->route('notices.index')->with('error', 'حدث خطأ أثناء حذف الإخطار: ' . $e->getMessage());
        }
    }

    public function downloadAttachment(Notice $notice, NoticeAttachment $attachment)
    {
        $filePath = storage_path('app/public/' . $attachment->file_path);
        if (file_exists($filePath)) {
            return response()->download($filePath, $attachment->file_name);
        }
        return back()->with('error', 'الملف غير موجود');
    }

    public function deleteAttachment(Notice $notice, NoticeAttachment $attachment)
    {
        try {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
            return back()->with('success', 'تم حذف المرفق بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف المرفق: ' . $e->getMessage());
        }
    }

    public function ajaxUserDataNotice(Request $request)
    {
        $identityNumber = $request->get('identity_number');
        if (!$identityNumber) return response()->json(['message' => 'رقم الهوية مطلوب'], 422);

        $mokalaf = Mokalaf::where('identity_number', $identityNumber)->first();
        if (!$mokalaf) return response()->json(['message' => 'لم يتم العثور على مكلف بهذه الهوية'], 404);

        $name = trim((string) ($mokalaf->name ?? ''));
        $isNameInvalid = $name === '' || $name === $mokalaf->identity_number
            || (preg_replace('/\s+/', '', $name) !== '' && ctype_digit(preg_replace('/\s+/', '', $name)));
        [$first, $second, $third, $last] = $this->splitFullName($name);

        if ($isNameInvalid) {
            try {
                $citizenResponse = app(CitizenController::class)->getCitizenForMokalaf($request);
                $data = $citizenResponse->getData(true);
                if (is_array($data) && (!empty($data['FNAME_ARB']) || !empty($data['LNAME_ARB']))) {
                    $first = $data['FNAME_ARB'] ?? '';
                    $second = $data['SNAME_ARB'] ?? '';
                    $third = $data['TNAME_ARB'] ?? '';
                    $last = $data['LNAME_ARB'] ?? '';
                    $fullName = trim(implode(' ', array_filter([$first, $second, $third, $last])));
                    if ($fullName !== '') $mokalaf->update(['name' => $fullName]);
                }
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'IDNO' => [
                'id' => $mokalaf->id,
                'identity_number' => $mokalaf->identity_number,
                'jebaia_no' => $mokalaf->license_number ?? '',
                'profile' => ['mobile' => $mokalaf->mobile ?? '', 'first_name' => $first, 'second_name' => $second, 'third_name' => $third, 'last_name' => $last],
            ],
            'name' => '<option value="' . e($mokalaf->id) . '" selected>' . e($mokalaf->name) . '</option>',
        ]);
    }

    public function ajaxNameDataNotice(Request $request)
    {
        $mokalafId = $request->get('name');
        if (!$mokalafId) return response()->json(['message' => 'المكلف غير محدد'], 422);
        $mokalaf = Mokalaf::find($mokalafId);
        if (!$mokalaf) return response()->json(['message' => 'لم يتم العثور على المكلف'], 404);
        [$first, $second, $third, $last] = $this->splitFullName($mokalaf->name);
        return response()->json([
            'IDNO' => [
                'id' => $mokalaf->id,
                'identity_number' => $mokalaf->identity_number,
                'profile' => ['mobile' => $mokalaf->mobile, 'first_name' => $first, 'second_name' => $second, 'third_name' => $third, 'last_name' => $last],
            ],
            'identity_number' => '<option value="' . e($mokalaf->identity_number) . '" selected>' . e($mokalaf->identity_number) . '</option>',
        ]);
    }

    public function ajaxJebaiaDataNotice(Request $request)
    {
        $subscription = $request->get('mokalaf');
        if (!$subscription) return response()->json(['message' => 'رقم الاشتراك مطلوب'], 422);
        $mokalaf = Mokalaf::where('license_number', $subscription)->first();
        if (!$mokalaf) return response()->json(['message' => 'لم يتم العثور على مكلف بهذا الرقم'], 404);
        [$first, $second, $third, $last] = $this->splitFullName($mokalaf->name);
        return response()->json([
            'IDNO' => [
                'id' => $mokalaf->id,
                'identity_number' => $mokalaf->identity_number,
                'profile' => ['mobile' => $mokalaf->mobile, 'first_name' => $first, 'second_name' => $second, 'third_name' => $third, 'last_name' => $last],
            ],
            'idNo' => '<option value="' . e($mokalaf->identity_number) . '" selected>' . e($mokalaf->identity_number) . '</option>',
            'userName' => '<option value="' . e($mokalaf->id) . '" selected>' . e($mokalaf->name) . '</option>',
            'prof_first_name' => $first, 'prof_second_name' => $second, 'prof_third_name' => $third, 'prof_last_name' => $last,
        ]);
    }
}

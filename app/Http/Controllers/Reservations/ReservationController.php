<?php

namespace App\Http\Controllers\Reservations;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Defendant;
use App\Models\Reservation;
use App\Models\ReservationAttachment;
use App\Models\Summoner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:index reservations', ['only' => ['index', 'show']]);
        $this->middleware('permission:store reservations', ['only' => ['store']]);
        $this->middleware('permission:update reservations', ['only' => ['update']]);
        $this->middleware('permission:delete reservations', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $reservations = Reservation::with(['creator'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reservation_no', 'like', '%' . $search . '%')
                      ->orWhere('reservation_number', 'like', '%' . $search . '%')
                      ->orWhere('subject', 'like', '%' . $search . '%')
                      ->orWhere('voucher', 'like', '%' . $search . '%')
                      ->orWhere('widget', 'like', '%' . $search . '%')
                      ->orWhere('address', 'like', '%' . $search . '%')
                      ->orWhere('jebaia_no', 'like', '%' . $search . '%');
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('reservation_date', [$fromDate, $toDate]);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        $query = request()->only(['identity_number', 'name', 'address', 'mobile']);
        return redirect()->route('reservations.index', $query)->with('openAddModal', true);
    }

    public function store(StoreReservationRequest $request)
    {
        if ($request->has('subject') && $request->has('reservation_no')) {
            return $this->storeAudiencePenFormat($request);
        }

        try {
            DB::beginTransaction();

            $reservationData = [
                'reservation_number' => $request->reservation_number,
                'reservation_date' => $request->reservation_date,
                'reservation_time' => $request->reservation_time,
                'court_name' => $request->court_name,
                'judge_name' => $request->judge_name,
                'case_number' => $request->case_number,
                'case_year' => $request->case_year,
                'description' => $request->description,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ];

            $reservation = Reservation::create($reservationData);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('reservation_attachments', 'public');
                    ReservationAttachment::create([
                        'reservation_id' => $reservation->id,
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
                return response()->json(['success' => true, 'message' => 'تم إضافة الحجز بنجاح']);
            }
            return redirect()->route('reservations.index')->with('success', 'تم إضافة الحجز بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء إضافة الحجز: ' . $e->getMessage(), 'errors' => $e->getMessage()], 422);
            }
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إضافة الحجز: ' . $e->getMessage());
        }
    }

    protected function storeAudiencePenFormat(Request $request)
    {
        try {
            DB::beginTransaction();

            $reservationNumber = 'R' . $request->reservation_no;
            if (Reservation::where('reservation_number', $reservationNumber)->exists()) {
                throw ValidationException::withMessages(['reservation_no' => ['رقم الحجز مستخدم مسبقاً.']]);
            }

            $reservation = Reservation::create([
                'reservation_number' => $reservationNumber,
                'reservation_date' => now(),
                'reservation_time' => now(),
                'court_name' => $request->subject ?: '—',
                'judge_name' => '—',
                'case_number' => '—',
                'case_year' => now()->year,
                'reservation_no' => $request->reservation_no,
                'subject' => $request->subject,
                'area' => $request->area,
                'voucher' => $request->voucher,
                'widget' => $request->widget,
                'address' => $request->address,
                'jebaia_no' => $request->jebaia_no,
                'license_file' => $request->license_file,
                'contents' => $request->contents,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            $this->saveSummoners($reservation, $request);
            $this->saveDefendants($reservation, $request);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم إضافة الحجز بنجاح']);
            }
            return redirect()->route('reservations.index')->with('success', 'تم إضافة الحجز بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء إضافة الحجز: ' . $e->getMessage(), 'errors' => ['exception' => [$e->getMessage()]]], 422);
            }
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إضافة الحجز: ' . $e->getMessage());
        }
    }

    protected function saveSummoners(Reservation $reserve, Request $request): void
    {
        $reserveSummoners = [];
        if ($request->summoner !== null && is_array($request->summoner)) {
            foreach ($request->summoner as $summoner) {
                $idno = $summoner['idno_summoner'] ?? null;
                $name = $summoner['name_summoner'] ?? null;
                if ($name === null || $name === '') continue;
                $summonerModel = Summoner::firstOrCreate(
                    ['identity_number' => $idno, 'name' => $name],
                    ['identity_number' => $idno, 'name' => $name]
                );
                $reserveSummoners[] = $summonerModel->id;
            }
        }
        $reserve->summoner()->sync(array_unique($reserveSummoners));
    }

    protected function saveDefendants(Reservation $reserve, Request $request): void
    {
        $reserveDefendants = [];
        if ($request->defendant !== null && is_array($request->defendant)) {
            foreach ($request->defendant as $defendant) {
                $idno = $defendant['idno_defendant'] ?? null;
                $name = $defendant['name_defendant'] ?? null;
                if ($name === null || $name === '') continue;
                $defendantModel = Defendant::firstOrCreate(
                    ['identity_number' => $idno, 'name' => $name],
                    ['identity_number' => $idno, 'name' => $name]
                );
                $reserveDefendants[] = $defendantModel->id;
            }
        }
        $reserve->defendant()->sync(array_unique($reserveDefendants));
    }

    public function show(Reservation $reservation)
    {
        return redirect()->route('reservations.edit', $reservation);
    }

    public function edit(Reservation $reservation)
    {
        $reservation->load(['attachments']);
        return view('reservations.edit', compact('reservation'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        if ($request->has('subject') && $request->has('reservation_no')) {
            return $this->updateAudiencePenFormat($request, $reservation);
        }

        try {
            DB::beginTransaction();

            $reservation->update([
                'reservation_number' => $request->reservation_number,
                'reservation_date' => $request->reservation_date,
                'reservation_time' => $request->reservation_time,
                'court_name' => $request->court_name,
                'judge_name' => $request->judge_name,
                'case_number' => $request->case_number,
                'case_year' => $request->case_year,
                'description' => $request->description,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('reservation_attachments', 'public');
                    ReservationAttachment::create([
                        'reservation_id' => $reservation->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('reservations.index')->with('success', 'تم تحديث الحجز بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الحجز: ' . $e->getMessage());
        }
    }

    protected function updateAudiencePenFormat(Request $request, Reservation $reservation)
    {
        try {
            DB::beginTransaction();

            $reservationNumber = 'R' . $request->reservation_no;
            if (Reservation::where('reservation_number', $reservationNumber)->where('id', '!=', $reservation->id)->exists()) {
                throw ValidationException::withMessages(['reservation_no' => ['رقم الحجز مستخدم مسبقاً.']]);
            }

            $reservation->update([
                'reservation_no' => $request->reservation_no,
                'reservation_number' => $reservationNumber,
                'subject' => $request->subject,
                'area' => $request->area,
                'voucher' => $request->voucher,
                'widget' => $request->widget,
                'address' => $request->address,
                'jebaia_no' => $request->jebaia_no,
                'license_file' => $request->license_file,
                'contents' => $request->contents,
                'court_name' => $request->subject ?: $reservation->court_name,
                'status' => $request->status ?: $reservation->status,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم تحديث الحجز بنجاح']);
            }
            return redirect()->route('reservations.index')->with('success', 'تم تحديث الحجز بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تحديث الحجز: ' . $e->getMessage(), 'errors' => ['exception' => [$e->getMessage()]]], 422);
            }
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الحجز: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        try {
            $reservation->delete();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم حذف الحجز بنجاح']);
            }
            return redirect()->route('reservations.index')->with('success', 'تم حذف الحجز بنجاح');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء حذف الحجز: ' . $e->getMessage(), 'errors' => ['exception' => [$e->getMessage()]]], 422);
            }
            return redirect()->route('reservations.index')->with('error', 'حدث خطأ أثناء حذف الحجز: ' . $e->getMessage());
        }
    }

    public function downloadAttachment(Reservation $reservation, ReservationAttachment $attachment)
    {
        $filePath = storage_path('app/public/' . $attachment->file_path);
        if (file_exists($filePath)) {
            return response()->download($filePath, $attachment->file_name);
        }
        return back()->with('error', 'الملف غير موجود');
    }

    public function deleteAttachment(Reservation $reservation, ReservationAttachment $attachment)
    {
        try {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
            return back()->with('success', 'تم حذف المرفق بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف المرفق: ' . $e->getMessage());
        }
    }
}

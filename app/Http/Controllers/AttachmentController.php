<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AttachmentController extends Controller
{

    /**
     * Helper: هل الطلب يريد JSON؟
     * يتحقق من عدة طرق لضمان اكتشاف AJAX بشكل صحيح
     */
    private function expectsJson(Request $request): bool
    {
        return $request->ajax()
            || $request->wantsJson()
            || $request->header('Accept') === 'application/json'
            || str_contains($request->header('Accept', ''), 'application/json');
    }

    public function store(Request $request)
    {
        try {
            // Check permission
            if (!auth()->check() || !auth()->user()->can('store attachment')) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بهذه العملية',
                ], 403);
            }

            $validatedData = $request->validate([
                'file_name'   => 'required|string|max:255',
                'file'        => 'required|file|mimes:pdf|max:20480',
                'employee_id' => 'required',
            ], [
                'required'      => __('required'),
                'file.mimes'    => __('الملف يجب أن يكون بصيغة PDF'),
                'file.max'      => __('حجم الملف يجب ألا يتجاوز 20 ميجابايت'),
                'string'        => __('string'),
                'max'           => __('الاسم يجب ألا يتجاوز 255 حرف'),
            ]);

            if ($request->hasFile('file')) {
                $validatedData['file'] = $request->file->store('employee_attachments', 'public');
            }

            $attachment = Attachment::create($validatedData);

            // Log activity with subject
            activity()
                ->performedOn($attachment)
                ->causedBy(auth()->user())
                ->log('تم إضافة مرفق جديد: ' . $validatedData['file_name']);

            // Always return JSON for AJAX requests
            return response()->json([
                'success' => true,
                'message' => __('saved successfully'),
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('يرجى تصحيح الأخطاء التالية'),
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Attachment store error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ المرفق: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Attachment $attachment)
    {
        try {
            // Check permission
            if (!auth()->check() || !auth()->user()->can('update attachment')) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بهذه العملية',
                ], 403);
            }

            $validatedData = $request->validate([
                'file_name' => 'required|string|max:255',
                'file'      => 'nullable|file|mimes:pdf|max:20480',
            ], [
                'required'      => __('required'),
                'file.mimes'    => __('الملف يجب أن يكون بصيغة PDF'),
                'file.max'      => __('حجم الملف يجب ألا يتجاوز 20 ميجابايت'),
                'string'        => __('string'),
                'max'           => __('الاسم يجب ألا يتجاوز 255 حرف'),
            ]);

            if ($request->hasFile('file')) {
                File::delete('storage/' . $attachment->file);
                $validatedData['file'] = $request->file->store('employee_attachments', 'public');
            }

            $attachment->update($validatedData);

            // Log activity with subject
            activity()
                ->performedOn($attachment)
                ->causedBy(auth()->user())
                ->log('تم تعديل المرفق: ' . $attachment->file_name);

            // Always return JSON for AJAX requests
            return response()->json([
                'success' => true,
                'message' => __('updated successfully'),
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('يرجى تصحيح الأخطاء التالية'),
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Attachment update error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المرفق: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Attachment $attachment)
    {
        try {
            // Check permission
            if (!auth()->check() || !auth()->user()->can('delete attachment')) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بهذه العملية',
                ], 403);
            }

            File::delete('storage/' . $attachment->file);
            $attachment->delete();

            // Log activity with subject
            activity()
                ->performedOn($attachment)
                ->causedBy(auth()->user())
                ->log('تم حذف المرفق: ' . $attachment->file_name);

            // Always return JSON for AJAX requests
            return response()->json([
                'success' => true,
                'message' => __('deleted successfully'),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Attachment delete error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المرفق: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download($id)
    {
        $attachment = Attachment::findOrFail($id);
        $filePath   = storage_path('app/public/' . $attachment->file);

        if (!File::exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
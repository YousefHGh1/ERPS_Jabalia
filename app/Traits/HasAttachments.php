<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait HasAttachments
{
    /**
     * معالجة المرفقات
     */
    public function handleAttachments(Model $model, array $files, string $folder = 'attachments'): void
    {
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            try {
                // التحقق من نوع الملف
                $allowedMimes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'image/jpeg',
                    'image/png',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ];

                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    Log::warning('Invalid file type uploaded', [
                        'file_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                    continue;
                }

                // حفظ الملف
                $path = $file->store($folder, 'public');

                // يجب أن يكون للـ Model علاقة attachments
                if (method_exists($model, 'attachments')) {
                    $model->attachments()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'created_by' => auth()->id(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error handling attachment', [
                    'model_type' => get_class($model),
                    'model_id' => $model->id,
                    'file_name' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * حذف المرفقات
     */
    public function deleteAttachments(Model $model): void
    {
        if (!method_exists($model, 'attachments')) {
            return;
        }

        foreach ($model->attachments as $attachment) {
            if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $attachment->delete();
        }
    }
}

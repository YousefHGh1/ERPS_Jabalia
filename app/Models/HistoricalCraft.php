<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HistoricalCraft extends Model
{
    protected $table = 'historical_crafts';
    protected $guarded = [];
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * تضييق أولي على مستوى قاعدة البيانات قبل التصفية الدقيقة في PHP:
     * - تاريخ الإنشاء ضمن النطاق المطلوب،
     * - رقم الملف: مطابقة جزئية حرفية على data->file_no مع بدائل المفاتيح.
     */
    public function scopePrefiltered(Builder $query, array $filters = []): Builder
    {
        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['file_number'])) {
            $like = '%' . escapeLike(trim((string) $filters['file_number'])) . '%';
            $query->where(function (Builder $q) use ($like) {
                $q->where('data->file_no', 'like', $like)
                    ->orWhere('data->file_number', 'like', $like)
                    ->orWhere('data->رقم الملف', 'like', $like);
            });
        }

        return $query;
    }
}

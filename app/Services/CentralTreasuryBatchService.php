<?php

namespace App\Services;

use App\Models\RevenueBatch;
use App\Repositories\RevenueBatchRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CentralTreasuryBatchService
{
    public function __construct(
        protected RevenueBatchRepository $batches
    ) {
    }

    /**
     * معرّف القسم المرتبط بمصدر إيراد (حرف / هندسة / طلبات قلم الجمهور).
     */
    public function resolveSectionIdForSource(string $sourceKey): int
    {
        $id = config("central_treasury.revenue_sources.{$sourceKey}.section_id");

        if (! $id) {
            throw new RuntimeException(
                'لم يُضبط قسم الصندوق المركزي لمصدر «' . $sourceKey . '». أضف المتغير المناسب في ملف .env (انظر config/central_treasury.php).'
            );
        }

        return (int) $id;
    }

    /**
     * جلب رزمة القسم المفتوحة أو إنشاؤها عند أول دفعة للقسم في اليوم.
     */
    public function getOrOpenSectionBatchForSource(string $sourceKey): RevenueBatch
    {
        $sectionId = $this->resolveSectionIdForSource($sourceKey);

        return $this->getOrOpenSectionBatch($sectionId);
    }

    public function getOrOpenSectionBatch(int $sectionId): RevenueBatch
    {
        $today = Carbon::today()->toDateString();

        return DB::transaction(function () use ($sectionId, $today) {
            $unified = RevenueBatch::query()
                ->where('batch_kind', RevenueBatch::KIND_UNIFIED)
                ->forDate($today)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            if (! $unified) {
                $unified = $this->createUnifiedBatch($today);
            } elseif (! $unified->isOpen()) {
                throw new RuntimeException('الرزمة الموحدة لهذا اليوم مغلقة. لا يمكن تسجيل دفعات جديدة.');
            }

            $sectionBatch = RevenueBatch::query()
                ->where('batch_kind', RevenueBatch::KIND_SECTION)
                ->where('parent_batch_id', $unified->id)
                ->where('section_id', $sectionId)
                ->lockForUpdate()
                ->first();

            if ($sectionBatch) {
                if (! $sectionBatch->isOpen()) {
                    throw new RuntimeException('رزمة تحصيل هذا القسم لتاريخ اليوم مغلقة.');
                }

                return $sectionBatch;
            }

            return $this->createSectionBatch($unified, $sectionId);
        });
    }

    protected function createUnifiedBatch(string $dateYmd): RevenueBatch
    {
        $uid = Auth::id();
        if (! $uid) {
            throw new RuntimeException('يجب تسجيل الدخول لفتح الرزمة الموحدة.');
        }

        $lastSequence = RevenueBatch::max('sequence_number');
        $nextSequence = $lastSequence ? $lastSequence + 1000 : 10000;

        return $this->batches->create([
            'date'               => $dateYmd,
            'batch_kind'         => RevenueBatch::KIND_UNIFIED,
            'parent_batch_id'    => null,
            'section_id'         => null,
            'status'             => 'open',
            'opened_by_user_id'  => $uid,
            'opened_at'          => now(),
            'total_amount'       => 0,
            'payments_count'     => 0,
            'sequence_number'    => $nextSequence,
        ]);
    }

    protected function createSectionBatch(RevenueBatch $unified, int $sectionId): RevenueBatch
    {
        $uid = Auth::id();
        if (! $uid) {
            throw new RuntimeException('يجب تسجيل الدخول لفتح رزمة القسم.');
        }

        $lastSequence = RevenueBatch::max('sequence_number');
        $nextSequence = $lastSequence ? $lastSequence + 1000 : 10000;

        return $this->batches->create([
            'date'               => $unified->date->toDateString(),
            'batch_kind'         => RevenueBatch::KIND_SECTION,
            'parent_batch_id'    => $unified->id,
            'section_id'         => $sectionId,
            'status'             => 'open',
            'opened_by_user_id'  => $uid,
            'opened_at'          => now(),
            'total_amount'       => 0,
            'payments_count'     => 0,
            'sequence_number'    => $nextSequence,
        ]);
    }

    public function closeSectionBatch(RevenueBatch $batch): RevenueBatch
    {
        if (! $batch->isSectionBatch()) {
            throw new RuntimeException('يمكن إغلاق رزم الأقسام فقط من هذه الشاشة.');
        }

        if (! $batch->isOpen()) {
            throw new RuntimeException('رزمة القسم مغلقة بالفعل.');
        }

        return DB::transaction(function () use ($batch) {
            $parentId = $batch->parent_batch_id;

            $batch->update([
                'status'             => 'closed',
                'closed_by_user_id'  => Auth::id(),
                'closed_at'          => now(),
            ]);

            if ($parentId) {
                $this->maybeAutoCloseUnified((int) $parentId);
            }

            return $batch->fresh();
        });
    }

    /**
     * إغلاق الرزمة الموحدة يدوياً عندما لا توجد رزم أقسام مفتوحة (مثل البيانات القديمة أو يوم بلا أقسام).
     */
    public function closeUnifiedBatchForToday(): RevenueBatch
    {
        $today = Carbon::today()->toDateString();

        $unified = RevenueBatch::query()
            ->where('batch_kind', RevenueBatch::KIND_UNIFIED)
            ->forDate($today)
            ->orderByDesc('id')
            ->first();

        if (! $unified) {
            throw new RuntimeException('لا توجد رزمة موحدة لهذا اليوم.');
        }

        if (! $unified->isOpen()) {
            throw new RuntimeException('الرزمة الموحدة مغلقة بالفعل.');
        }

        if ($unified->sectionBatches()->where('status', 'open')->exists()) {
            throw new RuntimeException('لا يمكن إغلاق الرزمة الموحدة قبل إغلاق جميع رزم الأقسام المفتوحة.');
        }

        return DB::transaction(function () use ($unified) {
            $unified->update([
                'status'             => 'closed',
                'closed_by_user_id'  => Auth::id(),
                'closed_at'          => now(),
            ]);

            return $unified->fresh();
        });
    }

    protected function maybeAutoCloseUnified(int $parentId): void
    {
        $parent = RevenueBatch::query()->whereKey($parentId)->lockForUpdate()->first();

        if (! $parent || ! $parent->isUnified() || ! $parent->isOpen()) {
            return;
        }

        $stillOpen = $parent->sectionBatches()->where('status', 'open')->exists();

        if ($stillOpen) {
            return;
        }

        $parent->update([
            'status'             => 'closed',
            'closed_by_user_id'  => Auth::id(),
            'closed_at'          => now(),
        ]);
    }

    public function reopenBatch(RevenueBatch $batch): RevenueBatch
    {
        if ($batch->isUnified()) {
            throw new RuntimeException('أعد فتح رزمة القسم وليس الرزمة الموحدة.');
        }

        if (! $batch->isSectionBatch()) {
            throw new RuntimeException('نوع الرزمة غير مدعوم لإعادة الفتح.');
        }

        if ($batch->isOpen()) {
            return $batch;
        }

        if ($batch->date && ! $batch->date->isToday()) {
            throw new RuntimeException('يُسمح بإعادة فتح رزم أقسام اليوم فقط من هذه الشاشة.');
        }

        return DB::transaction(function () use ($batch) {
            $parent = $batch->parent;
            if ($parent && ! $parent->isOpen()) {
                $parent->update([
                    'status'             => 'open',
                    'closed_by_user_id'  => null,
                    'closed_at'          => null,
                ]);
            }

            $batch->update([
                'status'             => 'open',
                'closed_by_user_id'  => null,
                'closed_at'          => null,
            ]);

            if (function_exists('activity')) {
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($batch)
                    ->withProperties([
                        'batch_id' => $batch->id,
                        'date'     => $batch->date?->format('Y-m-d') ?? (string) $batch->date,
                    ])
                    ->log('reopened_section_revenue_batch');
            }

            return $batch->fresh();
        });
    }
}

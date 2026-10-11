<?php

namespace App\Console\Commands;

use App\Models\RevenueBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CloseDailyBatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batches:close-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إغلاق الرزم اليومية المفتوحة تلقائياً في نهاية اليوم';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('جاري فحص الرزم اليومية المفتوحة...');

        // الحصول على جميع الرزم الموحدة المفتوحة لليوم
        $unifiedBatches = RevenueBatch::whereDate('date', now())
            ->where('batch_kind', RevenueBatch::KIND_UNIFIED)
            ->where('status', 'open')
            ->get();

        // الحصول على جميع رزم الأقسام المفتوحة لليوم
        $sectionBatches = RevenueBatch::whereDate('date', now())
            ->where('batch_kind', RevenueBatch::KIND_SECTION)
            ->where('status', 'open')
            ->get();

        $closedCount = 0;

        // إغلاق الرزم الموحدة
        foreach ($unifiedBatches as $batch) {
            $this->closeBatch($batch, 'موحدة');
            $closedCount++;
        }

        // إغلاق رزم الأقسام
        foreach ($sectionBatches as $batch) {
            $this->closeBatch($batch, 'قسم');
            $closedCount++;
        }

        if ($closedCount > 0) {
            $this->info("تم إغلاق {$closedCount} رزمة يومية بنجاح");
            Log::info("Daily batches auto-closed", [
                'date' => now()->format('Y-m-d'),
                'closed_count' => $closedCount,
                'unified_batches' => $unifiedBatches->count(),
                'section_batches' => $sectionBatches->count(),
            ]);
        } else {
            $this->info('لا توجد رزم يومية مفتوحة لإغلاقها');
        }

        return Command::SUCCESS;
    }

    /**
     * إغلاق رزمة معينة
     */
    private function closeBatch(RevenueBatch $batch, string $type)
    {
        DB::transaction(function () use ($batch, $type) {
            $batch->update([
                'status' => 'closed',
                'closed_by_user_id' => 1, // System user ID
                'closed_at' => now(),
            ]);

            $this->line("تم إغلاق الرزمة {$type} #{$batch->id}");
        });
    }
}

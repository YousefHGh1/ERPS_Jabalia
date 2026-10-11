<?php

namespace App\Console\Commands;

use App\Models\Request as RequestModel;
use App\Models\RequestResponse;
use App\Models\RequestType;
use App\Services\RequestService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackfillRequestWorkflowResponses extends Command
{
    protected $signature = 'requests:backfill-workflow-responses
        {--request= : رقم طلب محدد لإصلاحه فقط}
        {--dry : عرض ما سيتم إصلاحه دون تنفيذ}';

    protected $description = 'إنشاء الردود (المسارات) الناقصة للطلبات التي أُنشئت قبل إعداد مسارات نوع طلبها أو فشل إنشاؤها';

    public function handle(RequestService $requestService): int
    {
        $query = RequestModel::query()
            ->whereDoesntHave('responses')
            ->orderBy('id');

        if ($requestId = $this->option('request')) {
            $query->where('id', (int) $requestId);
        }

        $requests = $query->get();

        if ($requests->isEmpty()) {
            $this->info('✅ جميع الطلبات لديها مسارات مسجلة. لا يوجد شيء لإصلاحه.');
            return self::SUCCESS;
        }

        $this->info("عدد الطلبات الناقصة المسارات: {$requests->count()}");

        $fixed = 0;
        $skipped = 0;

        foreach ($requests as $request) {
            $workflowsCount = $request->requestType
                ? $request->requestType->workflows()->active()->count()
                : 0;

            $typeName = $request->requestType->name ?? "ID={$request->request_type_id}";

            if ($workflowsCount === 0) {
                $this->warn("⏭️  طلب #{$request->id} ({$typeName}): نوع الطلب بلا مسارات نشطة - تخطّي");
                $skipped++;
                continue;
            }

            if ($this->option('dry')) {
                $this->line("🔧 سيتم إصلاح طلب #{$request->id} ({$typeName}) - إضافة {$workflowsCount} مسار");
                $fixed++;
                continue;
            }

            try {
                $requestService->createWorkflowResponses($request);
                $this->info("✅ طلب #{$request->id} ({$typeName}): تمت إضافة {$workflowsCount} مسار");
                $fixed++;
            } catch (\Throwable $e) {
                $this->error("❌ طلب #{$request->id} ({$typeName}): {$e->getMessage()}");
                Log::error('Backfill workflow responses failed', [
                    'request_id' => $request->id,
                    'error' => $e->getMessage(),
                ]);
                $skipped++;
            }
        }

        $this->newLine();
        $this->info("تم إصلاح: {$fixed} | تم تخطي: {$skipped}");

        return self::SUCCESS;
    }
}

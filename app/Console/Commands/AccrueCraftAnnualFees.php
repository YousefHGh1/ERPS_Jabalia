<?php

namespace App\Console\Commands;

use App\Services\CraftLicenseService;
use Illuminate\Console\Command;

class AccrueCraftAnnualFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'craft:accrue-fees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ترصيد الرسوم السنوية لرخص الحرف للسنة الحالية لجميع الملفات النشطة';

    /**
     * Execute the console command.
     */
    public function handle(CraftLicenseService $craftLicenseService): int
    {
        $this->info('بدء ترصيد الرسوم السنوية لرخص الحرف للسنة الحالية...');

        $craftLicenseService->accrueAnnualFeesForCurrentYear();

        $this->info('تم ترصيد الرسوم السنوية لرخص الحرف بنجاح.');

        return Command::SUCCESS;
    }
}


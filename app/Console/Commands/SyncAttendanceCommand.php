<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:sync';

    protected $description = 'Sync attendance data from ZK machine';

    public function handle(\App\Services\AttendanceService $attendanceService)
    {
        $this->info('Starting attendance sync...');
        $count = $attendanceService->syncAttendance();
        
        if ($count === false) {
            $this->error('Failed to connect to ZK machine.');
            return 1;
        }

        $this->info("Successfully synced $count records.");
        return 0;
    }
}

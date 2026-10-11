<?php

namespace App\Services;

use App\Models\User;
use App\Models\ZKData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Rats\Zkteco\Lib\ZKTeco;

class AttendanceService
{
    protected $ip;

    public function __construct()
    {
        $this->ip = config('attendance.zkteco_ip', '192.168.2.223');
    }

    public function syncAttendance()
    {
        $startAt = microtime(true);
        $zk = new ZKTeco($this->ip);
        if (! $zk->connect()) {
            Log::warning('ZKTECO connect failed', ['ip' => $this->ip]);
            return false;
        }

        $zkAttendanceLogs = $zk->getAttendance();
        $zk->disconnect();

        if (! is_array($zkAttendanceLogs) && ! $zkAttendanceLogs instanceof \Traversable) {
            return 0;
        }

        $logs = collect($zkAttendanceLogs);
        if ($logs->isEmpty()) {
            return 0;
        }

        $uids = $logs->pluck('uid')->unique()->filter(fn ($uid) => $uid !== null && $uid !== '')->values();
        $existingUids = $uids->isNotEmpty()
            ? ZKData::query()->whereIn('uid', $uids)->pluck('uid')->all()
            : [];
        $existingUidSet = array_flip(array_map('strval', $existingUids));

        $employeeNumbers = $logs->pluck('id')->unique()->filter(fn ($id) => $id !== null && $id !== '')->values();
        $employees = collect();
        if ($employeeNumbers->isNotEmpty()) {
            $employees = User::query()
                ->whereIn('employee_number', $employeeNumbers)
                ->with('employee.EmployeeJobInfo.shifts')
                ->get()
                ->keyBy(fn (User $u) => (string) $u->employee_number);
        }

        $syncedCount = 0;
        $toInsert = [];
        $chunkSize = 500;
        $insertNow = now();

        foreach ($logs as $data) {
            $uidKey = isset($data['uid']) ? (string) $data['uid'] : '';
            if ($uidKey === '' || isset($existingUidSet[$uidKey])) {
                continue;
            }

            $empKey = isset($data['id']) ? (string) $data['id'] : '';
            /** @var User|null $employee */
            $employee = $empKey !== '' ? $employees->get($empKey) : null;

            if (! $employee || ! $employee->employee || ! $employee->employee->EmployeeJobInfo) {
                continue;
            }

            $shifts = $employee->employee->EmployeeJobInfo->shifts;
            $carbonDate = Carbon::parse($data['timestamp']);
            $dayOfWeek = $carbonDate->format('l');

            foreach ($shifts as $shift) {
                if (! in_array($dayOfWeek, $shift->days, true)) {
                    continue;
                }

                $timeString = $carbonDate->format('H:i');
                if (! ($timeString < $shift->end_time)) {
                    continue;
                }

                if ((int) $data['type'] === 0) {
                    $date1 = Carbon::parse($data['timestamp'])->setDate(now()->year, now()->month, now()->day);
                    $date2 = Carbon::parse($shift->getLateStartTimestamp())->setDate(now()->year, now()->month, now()->day);

                    $late_difference = $date1->diff($date2);
                    $minutesDifference = ($date1->format('H:i') > $date2->format('H:i'))
                        ? $late_difference->h * 60 + $late_difference->i
                        : 0;

                    if ($minutesDifference >= 0) {
                        $toInsert[] = [
                            'uid' => $data['uid'],
                            'emp_id' => $employee->employee_number,
                            'state' => $data['state'],
                            'arrival_time' => $data['timestamp'],
                            'type' => $data['type'],
                            'late_minutes' => (int) $minutesDifference,
                            'created_at' => $insertNow,
                            'updated_at' => $insertNow,
                        ];
                        $syncedCount++;

                        if (count($toInsert) >= $chunkSize) {
                            ZKData::query()->insert($toInsert);
                            $toInsert = [];
                        }
                    }
                } elseif ((int) $data['type'] === 1) {
                    $date1 = Carbon::parse($data['timestamp'])->setDate(now()->year, now()->month, now()->day);
                    $date2 = Carbon::parse($shift->getLateEndTimestamp())->setDate(now()->year, now()->month, now()->day);

                    $late_difference = $date1->diff($date2);
                    $minutesDifference = ($date1->format('H:i') > $date2->format('H:i'))
                        ? $late_difference->h * 60 + $late_difference->i
                        : 0;

                    if ($minutesDifference > 0) {
                        $toInsert[] = [
                            'uid' => $data['uid'],
                            'emp_id' => $employee->employee_number,
                            'state' => $data['state'],
                            'arrival_time' => $data['timestamp'],
                            'type' => $data['type'],
                            'early_departure' => (int) $minutesDifference,
                            'created_at' => $insertNow,
                            'updated_at' => $insertNow,
                        ];
                        $syncedCount++;

                        if (count($toInsert) >= $chunkSize) {
                            ZKData::query()->insert($toInsert);
                            $toInsert = [];
                        }
                    }
                }
            }
        }

        if (!empty($toInsert)) {
            ZKData::query()->insert($toInsert);
        }

        $durationMs = (int) round((microtime(true) - $startAt) * 1000);
        Log::info('ZKTECO attendance sync completed', [
            'records_inserted' => $syncedCount,
            'duration_ms' => $durationMs,
            'ip' => $this->ip,
        ]);

        return $syncedCount;
    }
}

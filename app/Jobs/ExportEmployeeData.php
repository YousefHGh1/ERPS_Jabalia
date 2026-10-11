<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportEmployeeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $filters;
    public string $filename;

    /**
     * Create a new job instance.
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->filename = 'employees_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Employee::with('department', 'section', 'branch', 'job');

        // Apply filters
        if (!empty($this->filters['name'])) {
            $query->where('name', 'like', '%' . $this->filters['name'] . '%');
        }
        if (!empty($this->filters['czn_id'])) {
            $query->where('czn_id', 'like', '%' . $this->filters['czn_id'] . '%');
        }
        if (!empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }
        if (!empty($this->filters['section_id'])) {
            $query->where('section_id', $this->filters['section_id']);
        }
        if (!empty($this->filters['branch_id'])) {
            $query->where('branch_id', $this->filters['branch_id']);
        }
        if (!empty($this->filters['job_id'])) {
            $query->where('job_id', $this->filters['job_id']);
        }

        $employees = $query->get();

        $csvContent = $this->generateCsv($employees);

        Storage::disk('exports')->put($this->filename, $csvContent);
    }

    /**
     * Generate CSV content.
     */
    protected function generateCsv($employees): string
    {
        $file = fopen('php://temp', 'r+');
        fputs($file, "\xEF\xBB\xBF"); // BOM for UTF-8

        // Header
        fputcsv($file, [
            __('id'),
            __('name'),
            __('czn_id'),
            __('department'),
            __('section'),
            __('branch'),
            __('job'),
            __('mobile'),
            __('salary'),
            __('created_at')
        ]);

        foreach ($employees as $emp) {
            fputcsv($file, [
                $emp->id,
                $emp->name,
                $emp->czn_id,
                $emp->department?->name,
                $emp->section?->name,
                $emp->branch?->name,
                $emp->job?->name,
                $emp->mobile,
                $emp->salary,
                $emp->created_at
            ]);
        }

        rewind($file);
        $content = stream_get_contents($file);
        fclose($file);

        return $content;
    }
}

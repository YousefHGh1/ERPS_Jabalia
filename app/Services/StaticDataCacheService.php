<?php
namespace App\Services;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\Job;
use App\Models\Section;
use App\Models\Shift;
use Illuminate\Support\Facades\Cache;

class StaticDataCacheService
{
    /**
     * Cache duration in seconds (1 hour)
     */
    protected int $cacheDuration = 3600;

    /**
     * Get all departments with caching.
     */
    public function getDepartments(array $columns = ['id', 'name'])
    {
        return Cache::remember('departments.all', $this->cacheDuration, function () use ($columns) {
            return Department::get($columns);
        });
    }

    /**
     * Get all sections with caching.
     */
    public function getSections(array $columns = ['id', 'name', 'department_id'])
    {
        $cacheKey = 'sections.all.' . md5(implode(',', $columns));

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($columns) {
            return Section::get($columns);
        });
    }

    /**
     * Get all branches with caching.
     */
    public function getBranches(array $columns = ['id', 'name', 'section_id'])
    {
        $cacheKey = 'branches.all.' . md5(implode(',', $columns));

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($columns) {
            return Branch::get($columns);
        });
    }

    /**
     * Get all jobs with caching.
     */
    public function getJobs(array $columns = ['id', 'name'])
    {
        return Cache::remember('jobs.all', $this->cacheDuration, function () use ($columns) {
            return Job::get($columns);
        });
    }

    /**
     * Get all shifts with caching.
     */
    public function getShifts(array $columns = ['id', 'name'])
    {
        return Cache::remember('shifts.all', $this->cacheDuration, function () use ($columns) {
            return Shift::get($columns);
        });
    }

    /**
     * Get all holidays with caching.
     */
    public function getHolidays(array $columns = ['id', 'name'])
    {
        return Cache::remember('holidays.all', $this->cacheDuration, function () use ($columns) {
            return Holiday::get($columns);
        });
    }

    /**
     * Get sections by department with caching.
     */
    public function getSectionsByDepartment(int $departmentId)
    {
        return Cache::remember("sections.department_{$departmentId}", $this->cacheDuration, function () use ($departmentId) {
            return Section::where('department_id', $departmentId)->get(['id', 'name']);
        });
    }

    /**
     * Get branches by section with caching.
     */
    public function getBranchesBySection(int $sectionId)
    {
        return Cache::remember("branches.section_{$sectionId}", $this->cacheDuration, function () use ($sectionId) {
            return Branch::where('section_id', $sectionId)->get(['id', 'name']);
        });
    }

    /**
     * Clear all static data cache.
     */
    public function clearAll(): void
    {
        $keys = [
            'departments.all',
            'sections.all',
            'branches.all',
            'jobs.all',
            'shifts.all',
            'holidays.all',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Clear department-specific sections cache
        $departmentIds = Department::pluck('id');
        foreach ($departmentIds as $id) {
            Cache::forget("sections.department_{$id}");
        }

        // Clear section-specific branches cache
        $sectionIds = Section::pluck('id');
        foreach ($sectionIds as $id) {
            Cache::forget("branches.section_{$id}");
        }
    }

    /**
     * Clear specific cache key.
     */
    public function clear(string $key): void
    {
        Cache::forget($key);
    }
}

<?php

namespace Tests\Unit;

use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardService $dashboardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = new DashboardService();
    }

    /** @test */
    public function it_can_get_administrative_stats(): void
    {
        $jobId = $this->createJob();
        $departmentId = $this->createDepartment();
        $sectionId = $this->createSection($departmentId);
        $branchId = $this->createBranch($sectionId);

        DB::table('employees')->insert([
            'czn_id' => '10001',
            'name' => 'Test Employee',
            'email' => 'employee@example.com',
            'gender' => 'male',
            'status' => 'active',
            'address' => 'Test Address',
            'mobile' => '0599000001',
            'birthdate' => '1990-01-01',
            'birthplace' => 'Gaza',
            'salary' => 1000,
            'department_id' => $departmentId,
            'section_id' => $sectionId,
            'branch_id' => $branchId,
            'job_id' => $jobId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('vehicles')->insert([
            'number' => '1234',
            'name' => 'Municipal Vehicle',
            'color' => 'white',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stats = $this->dashboardService->getAdministrativeStats();

        $this->assertIsArray($stats);
        $this->assertSame(1, $stats['employees_count']);
        $this->assertSame(1, $stats['jobs_count']);
        $this->assertSame(1, $stats['vehicles_count']);
    }

    /** @test */
    public function it_can_get_crafts_stats(): void
    {
        DB::table('craft_licenses')->insert([
            'file_number' => 'CL0001',
            'status' => 'active',
            'classification' => 'A',
            'craft_type' => 'Test Craft',
            'registration_date' => '2026-01-01',
            'remaining' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('payments')->insert([
            'craft_license_id' => 1,
            'amount' => 500,
            'payment_date' => '2026-01-01',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stats = $this->dashboardService->getCraftsStats();

        $this->assertIsArray($stats);
        $this->assertSame(1, $stats['craft_licenses_count']);
        $this->assertSame(1, $stats['craft_payments_count']);
        $this->assertEquals(500, $stats['craft_revenue']);
    }

    /** @test */
    public function it_can_get_engineering_stats(): void
    {
        $userIds = $this->createUserContext();

        DB::table('engineerings')->insert([
            'file_number' => 'ENG-001',
            'remaining_amount' => 1000,
            'created_by' => $userIds['user_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('payment_engineerings')->insert([
            'engineering_id' => 1,
            'amount' => 750,
            'balance_after' => 250,
            'payment_date' => '2026-01-01',
            'payment_method' => 'cash',
            'status' => 'active',
            'created_by' => $userIds['user_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stats = $this->dashboardService->getEngineeringStats();

        $this->assertIsArray($stats);
        $this->assertSame(1, $stats['engineering_count']);
        $this->assertSame(1, $stats['engineering_payments_count']);
        $this->assertEquals(750, $stats['engineering_revenue']);
    }

    /** @test */
    public function it_caches_administrative_stats(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('dashboard_administrative_stats', 3600, \Closure::class)
            ->andReturn(['employees_count' => 5]);

        $stats = $this->dashboardService->getAdministrativeStats();

        $this->assertEquals(['employees_count' => 5], $stats);
    }

    /** @test */
    public function it_can_clear_dashboard_cache(): void
    {
        Cache::put('dashboard_administrative_stats', 'test_value');
        Cache::put('dashboard_crafts_stats', 'test_value');

        $this->dashboardService->clearDashboardCache();

        $this->assertNull(Cache::get('dashboard_administrative_stats'));
        $this->assertNull(Cache::get('dashboard_crafts_stats'));
    }

    /** @test */
    public function it_can_clear_specific_cache(): void
    {
        Cache::put('dashboard_administrative_stats', 'admin_value');
        Cache::put('dashboard_crafts_stats', 'crafts_value');

        $this->dashboardService->clearSpecificCache('administrative');

        $this->assertNull(Cache::get('dashboard_administrative_stats'));
        $this->assertNotNull(Cache::get('dashboard_crafts_stats'));
    }

    /** @test */
    public function it_can_get_complete_administrative_dashboard_data(): void
    {
        $data = $this->dashboardService->getAdministrativeDashboardData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('stats', $data);
        $this->assertArrayHasKey('latest_employees', $data);
        $this->assertArrayHasKey('latest_holidays', $data);
        $this->assertArrayHasKey('attendance_data', $data);
        $this->assertArrayHasKey('dept_distribution', $data);
    }

    /** @test */
    public function it_can_get_complete_crafts_dashboard_data(): void
    {
        $data = $this->dashboardService->getCraftsDashboardData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('stats', $data);
        $this->assertArrayHasKey('latest_craft_licenses', $data);
        $this->assertArrayHasKey('craft_category_data', $data);
        $this->assertArrayHasKey('craft_status_data', $data);
    }

    /** @test */
    public function it_can_get_complete_engineering_dashboard_data(): void
    {
        $data = $this->dashboardService->getEngineeringDashboardData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('stats', $data);
        $this->assertArrayHasKey('latest_engineering_files', $data);
        $this->assertArrayHasKey('engineering_status_data', $data);
        $this->assertArrayHasKey('revenue_by_month', $data);
    }

    protected function createCategory(): int
    {
        return DB::table('categories')->insertGetId([
            'name' => 'Category',
            'start_degree' => '1',
            'end_degree' => '10',
            'bonus_percent' => '5',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createJob(): int
    {
        return DB::table('jobs')->insertGetId([
            'name' => 'Job',
            'category_id' => $this->createCategory(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createDepartment(): int
    {
        return DB::table('departments')->insertGetId([
            'name' => 'Department',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createSection(int $departmentId): int
    {
        return DB::table('sections')->insertGetId([
            'name' => 'Section',
            'department_id' => $departmentId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createBranch(int $sectionId): int
    {
        return DB::table('branches')->insertGetId([
            'name' => 'Branch',
            'section_id' => $sectionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @return array{employee_id:int,user_id:int}
     */
    protected function createUserContext(): array
    {
        $jobId = $this->createJob();
        $departmentId = $this->createDepartment();
        $sectionId = $this->createSection($departmentId);
        $branchId = $this->createBranch($sectionId);

        $employeeId = DB::table('employees')->insertGetId([
            'czn_id' => '10002',
            'name' => 'User Employee',
            'email' => 'user-employee@example.com',
            'gender' => 'male',
            'status' => 'active',
            'address' => 'Test Address',
            'mobile' => '0599000002',
            'birthdate' => '1991-01-01',
            'birthplace' => 'Gaza',
            'salary' => 1500,
            'department_id' => $departmentId,
            'section_id' => $sectionId,
            'branch_id' => $branchId,
            'job_id' => $jobId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId = DB::table('users')->insertGetId([
            'name' => 'Dashboard User',
            'email' => 'dashboard-user@example.com',
            'password' => bcrypt('password'),
            'employee_number' => 'EMP-2001',
            'employee_id' => $employeeId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['employee_id' => $employeeId, 'user_id' => $userId];
    }
}

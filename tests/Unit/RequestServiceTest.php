<?php

namespace Tests\Unit;

use App\Events\RequestCreated;
use App\Exceptions\RequestWorkflowException;
use App\Models\Mokalaf;
use App\Models\Request as RequestModel;
use App\Models\RequestAttachment;
use App\Models\RequestResponse;
use App\Models\RequestStatus;
use App\Models\RequestType;
use App\Models\RequestWorkflow;
use App\Models\ResponseType;
use App\Models\User;
use App\Services\RequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RequestServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RequestService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(RequestService::class);
        Storage::fake('public');
        Event::fake();
    }

    /** @test */
    public function test_can_create_request(): void
    {
        $context = $this->createRequestContext();
        $this->createWorkflow($context['request_type']->id, $context['section_id']);

        $this->actingAs($context['user']);

        $request = $this->service->createRequest([
            'text' => 'Test request',
            'request_type_id' => $context['request_type']->id,
            'mokalaf_id' => $context['mokalaf']->id,
            'status_id' => $context['status']->id,
            'created_by' => $context['user']->id,
        ]);

        $this->assertInstanceOf(RequestModel::class, $request);
        $this->assertSame('Test request', $request->text);
        $this->assertSame($context['request_type']->id, $request->request_type_id);
        $this->assertSame($context['mokalaf']->id, $request->mokalaf_id);
        $this->assertDatabaseCount('request_responses', 1);

        Event::assertDispatched(RequestCreated::class);
    }

    /** @test */
    public function test_can_create_request_with_attachments(): void
    {
        $context = $this->createRequestContext();
        $this->createWorkflow($context['request_type']->id, $context['section_id']);

        $this->actingAs($context['user']);

        $request = $this->service->createRequest([
            'text' => 'Request with files',
            'request_type_id' => $context['request_type']->id,
            'mokalaf_id' => $context['mokalaf']->id,
            'status_id' => $context['status']->id,
            'created_by' => $context['user']->id,
        ], [
            UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->image('photo.jpg'),
        ]);

        $this->assertSame(2, $request->attachments()->count());
    }

    /** @test */
    public function test_create_request_throws_exception_when_workflow_not_configured(): void
    {
        $context = $this->createRequestContext();
        $this->actingAs($context['user']);

        $this->expectException(RequestWorkflowException::class);

        $this->service->createRequest([
            'text' => 'Test request',
            'request_type_id' => $context['request_type']->id,
            'mokalaf_id' => $context['mokalaf']->id,
            'status_id' => $context['status']->id,
            'created_by' => $context['user']->id,
        ]);
    }

    /** @test */
    public function test_can_update_request(): void
    {
        $context = $this->createRequestContext();

        $request = RequestModel::create([
            'text' => 'Original text',
            'request_type_id' => $context['request_type']->id,
            'mokalaf_id' => $context['mokalaf']->id,
            'status_id' => $context['status']->id,
            'created_by' => $context['user']->id,
        ]);

        $updatedRequest = $this->service->updateRequest($request, [
            'text' => 'Updated text',
        ]);

        $this->assertSame('Updated text', $updatedRequest->text);
    }

    /** @test */
    public function test_can_delete_request(): void
    {
        $context = $this->createRequestContext();

        $request = RequestModel::create([
            'text' => 'Test request to delete',
            'request_type_id' => $context['request_type']->id,
            'mokalaf_id' => $context['mokalaf']->id,
            'status_id' => $context['status']->id,
            'created_by' => $context['user']->id,
        ]);

        RequestAttachment::create([
            'request_id' => $request->id,
            'file_name' => 'test.pdf',
            'file_path' => 'test/file.pdf',
            'file_size' => 100,
            'mime_type' => 'application/pdf',
            'created_by' => $context['user']->id,
        ]);

        Storage::disk('public')->put('test/file.pdf', 'test content');

        $result = $this->service->deleteRequest($request);

        $this->assertTrue($result);
        $this->assertSoftDeleted('requests', ['id' => $request->id]);
    }

    /** @test */
    public function test_handle_attachments_skips_invalid_file_types(): void
    {
        $context = $this->createRequestContext();

        $request = RequestModel::create([
            'text' => 'Test request',
            'request_type_id' => $context['request_type']->id,
            'mokalaf_id' => $context['mokalaf']->id,
            'status_id' => $context['status']->id,
            'created_by' => $context['user']->id,
        ]);

        $invalidFile = UploadedFile::fake()->create('script.exe', 100, 'application/octet-stream');

        $this->service->handleAttachments($request, [$invalidFile]);

        $this->assertSame(0, $request->fresh()->attachments->count());
    }

    /** @test */
    public function test_handle_attachments_processes_valid_file_types(): void
    {
        $context = $this->createRequestContext();
        $this->actingAs($context['user']);

        $request = RequestModel::create([
            'text' => 'Test request',
            'request_type_id' => $context['request_type']->id,
            'mokalaf_id' => $context['mokalaf']->id,
            'status_id' => $context['status']->id,
            'created_by' => $context['user']->id,
        ]);

        $pdfFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $imageFile = UploadedFile::fake()->image('photo.jpg');

        $this->service->handleAttachments($request, [$pdfFile, $imageFile]);

        $this->assertSame(2, $request->fresh()->attachments->count());
    }

    /**
     * @return array{user:User,request_type:RequestType,status:RequestStatus,mokalaf:Mokalaf,section_id:int}
     */
    protected function createRequestContext(): array
    {
        $user = $this->createUser();
        $status = RequestStatus::create([
            'name' => 'قيد الانتظار',
            'color' => 'warning',
        ]);
        $requestType = RequestType::create([
            'name' => 'ترخيص بناء',
        ]);
        $mokalaf = Mokalaf::create([
            'name' => 'Test Mokalaf',
            'identity_number' => '123456789',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        foreach ([
            1 => 'موافق',
            2 => 'معلق',
            3 => 'مرفوض',
            4 => 'قيد الانتظار',
        ] as $id => $name) {
            ResponseType::create([
                'id' => $id,
                'name' => $name,
                'created_by' => $user->id,
            ]);
        }

        return [
            'user' => $user,
            'request_type' => $requestType,
            'status' => $status,
            'mokalaf' => $mokalaf,
            'section_id' => $this->createSection(),
        ];
    }

    protected function createWorkflow(int $requestTypeId, int $sectionId): RequestWorkflow
    {
        return RequestWorkflow::create([
            'request_type_id' => $requestTypeId,
            'previous_workflow_id' => null,
            'section_id' => $sectionId,
            'name' => 'Main workflow',
            'step_order' => 1,
            'is_active' => true,
        ]);
    }

    protected function createUser(): User
    {
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Category',
            'start_degree' => '1',
            'end_degree' => '10',
            'bonus_percent' => '5',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $jobId = DB::table('jobs')->insertGetId([
            'name' => 'Job',
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $departmentId = DB::table('departments')->insertGetId([
            'name' => 'Department',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $sectionId = DB::table('sections')->insertGetId([
            'name' => 'Section',
            'department_id' => $departmentId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $branchId = DB::table('branches')->insertGetId([
            'name' => 'Branch',
            'section_id' => $sectionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $employeeId = DB::table('employees')->insertGetId([
            'czn_id' => '99999',
            'name' => 'Test Employee',
            'email' => 'test-user@example.com',
            'gender' => 'male',
            'status' => 'active',
            'address' => 'Address',
            'mobile' => '0599000010',
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

        return User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'employee_number' => 'EMP-1001',
            'employee_id' => $employeeId,
            'password' => bcrypt('password'),
        ]);
    }

    protected function createSection(): int
    {
        $departmentId = DB::table('departments')->insertGetId([
            'name' => 'Workflow Department ' . uniqid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('sections')->insertGetId([
            'name' => 'Workflow Section ' . uniqid(),
            'department_id' => $departmentId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

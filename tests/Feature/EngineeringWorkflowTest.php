<?php

namespace Tests\Feature;

use App\Models\Engineering;
use App\Models\Employee;
use App\Models\Mokalaf;
use App\Models\PaymentEngineering;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EngineeringWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $engineer;
    protected Mokalaf $mokalaf;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with permissions
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->engineer = User::factory()->create();
        $this->engineer->givePermissionTo(['index engineerings', 'store engineerings', 'update engineerings']);

        // Create mokalaf
        $this->mokalaf = Mokalaf::factory()->create();

        Permission::findOrCreate('index engineerings');
        Permission::findOrCreate('store engineerings');
        Permission::findOrCreate('update engineerings');
        Permission::findOrCreate('delete engineerings');
    }

    /**
     * Test engineering file creation workflow
     * @test
     */
    public function can_create_engineering_file_with_workflow()
    {
        $this->actingAs($this->engineer);

        $data = [
            'mokalaf_id' => $this->mokalaf->id,
            'file_number' => 'ENG-' . $this->faker->unique()->numberBetween(1000, 9999),
            'subject' => 'بناء منزل سكني',
            'location' => 'حي النخيل',
            'fee_amount' => 5000.00,
            'description' => 'طلب ترخيص بناء منزل سكني جديد',
        ];

        $response = $this->post(route('engineerings.store'), $data);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('engineerings', [
            'mokalaf_id' => $this->mokalaf->id,
            'file_number' => $data['file_number'],
            'fee_amount' => 5000.00,
            'remaining_amount' => 5000.00,
        ]);
    }

    /**
     * Test engineering payment workflow
     * @test
     */
    public function can_process_engineering_payment_workflow()
    {
        $engineering = Engineering::factory()->create([
            'fee_amount' => 3000.00,
            'remaining_amount' => 3000.00,
        ]);

        $this->actingAs($this->engineer);

        // Add first payment
        $paymentData = [
            'amount' => 1500.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
            'notes' => 'دفعة أولى',
        ];

        $response = $this->post(route('engineerings.add-payment', $engineering), $paymentData);

        $response->assertJson(['success' => true]);

        $engineering->refresh();
        $this->assertEquals(1500.00, $engineering->paid_amount);
        $this->assertEquals(1500.00, $engineering->remaining_amount);

        // Add final payment
        $finalPaymentData = [
            'amount' => 1500.00,
            'payment_method' => 'bank',
            'payment_date' => now()->toDateString(),
            'notes' => 'دفعة أخيرة',
        ];

        $this->post(route('engineerings.add-payment', $engineering), $finalPaymentData);

        $engineering->refresh();
        $this->assertEquals(3000.00, $engineering->paid_amount);
        $this->assertEquals(0.00, $engineering->remaining_amount);
    }

    /**
     * Test engineering file status transitions
     * @test
     */
    public function can_update_engineering_file_status()
    {
        $engineering = Engineering::factory()->create(['status' => 'pending']);

        $this->actingAs($this->engineer);

        $response = $this->put(route('engineerings.update-status', $engineering), [
            'status' => 'under_review',
            'notes' => 'تم استلام الملف للمراجعة',
        ]);

        $response->assertRedirect();

        $engineering->refresh();
        $this->assertEquals('under_review', $engineering->status);
    }

    /**
     * Test engineering file search and filtering
     * @test
     */
    public function can_search_and_filter_engineering_files()
    {
        Engineering::factory()->create([
            'file_number' => 'ENG-1001',
            'status' => 'pending',
            'mokalaf_id' => $this->mokalaf->id,
        ]);

        Engineering::factory()->create([
            'file_number' => 'ENG-1002',
            'status' => 'completed',
            'mokalaf_id' => $this->mokalaf->id,
        ]);

        $this->actingAs($this->engineer);

        // Search by file number
        $response = $this->get(route('engineerings.index', ['search' => 'ENG-1001']));
        $response->assertSuccessful();

        // Filter by status
        $response = $this->get(route('engineerings.index', ['status' => 'completed']));
        $response->assertSuccessful();

        // Filter by mokalaf
        $response = $this->get(route('engineerings.index', ['mokalaf_id' => $this->mokalaf->id]));
        $response->assertSuccessful();
    }

    /**
     * Test engineering file permissions
     * @test
     */
    public function engineering_file_permissions_work_correctly()
    {
        $engineering = Engineering::factory()->create();

        // Test unauthorized access
        $unauthorizedUser = User::factory()->create();
        $this->actingAs($unauthorizedUser);

        $response = $this->get(route('engineerings.index'));
        $response->assertForbidden();

        $response = $this->post(route('engineerings.store'), []);
        $response->assertForbidden();

        // Test authorized access
        $this->actingAs($this->engineer);

        $response = $this->get(route('engineerings.index'));
        $response->assertSuccessful();
    }

    /**
     * Test engineering file deletion with payments
     * @test
     */
    public function can_delete_engineering_file_with_payments()
    {
        $engineering = Engineering::factory()->create();
        PaymentEngineering::factory()->create([
            'engineering_id' => $engineering->id,
            'amount' => 1000.00,
            'status' => 'active',
        ]);

        $this->actingAs($this->admin);

        $response = $this->delete(route('engineerings.destroy', $engineering));

        $response->assertRedirect();
        $this->assertSoftDeleted('engineerings', ['id' => $engineering->id]);
    }

    /**
     * Test engineering file statistics
     * @test
     */
    public function can_get_engineering_statistics()
    {
        Engineering::factory()->count(5)->create(['status' => 'pending']);
        Engineering::factory()->count(3)->create(['status' => 'completed']);
        Engineering::factory()->count(2)->create(['remaining_amount' => 1000.00]);

        $this->actingAs($this->engineer);

        $response = $this->get(route('engineerings.stats'));

        $response->assertJson([
            'total' => 10,
            'pending' => 5,
            'completed' => 3,
            'pending_payments' => 2,
        ]);
    }
}

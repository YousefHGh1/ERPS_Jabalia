<?php

namespace Tests\Feature;

use App\Models\CraftLicense;
use App\Models\Payment;
use App\Models\RequestPayment;
use App\Models\Request as RequestModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PaymentProcessingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $cashier;
    protected RequestModel $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->cashier = User::factory()->create();
        $this->cashier->givePermissionTo(['process payments', 'cancel payments']);

        $this->request = RequestModel::factory()->create([
            'fee_amount' => 1000.00,
            'paid_amount' => 0,
            'remaining_amount' => 1000.00,
        ]);

        Permission::findOrCreate('process payments');
        Permission::findOrCreate('cancel payments');
        Permission::findOrCreate('view payments');
    }

    /**
     * Test processing request payment
     * @test
     */
    public function can_process_request_payment_successfully()
    {
        $this->actingAs($this->cashier);

        $paymentData = [
            'amount' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
            'receipt_number' => 'RCP-' . $this->faker->unique()->numberBetween(1000, 9999),
            'notes' => 'دفعة جزئية',
        ];

        $response = $this->post(route('requests.add-payment', $this->request), $paymentData);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('request_payments', [
            'request_id' => $this->request->id,
            'amount' => 500.00,
            'payment_method' => 'cash',
            'status' => 'active',
        ]);

        $this->request->refresh();
        $this->assertEquals(500.00, $this->request->paid_amount);
        $this->assertEquals(500.00, $this->request->remaining_amount);
    }

    /**
     * Test payment validation rules
     * @test
     */
    public function payment_validation_works_correctly()
    {
        $this->actingAs($this->cashier);

        // Test payment exceeding remaining amount
        $response = $this->post(route('requests.add-payment', $this->request), [
            'amount' => 1500.00, // More than remaining (1000)
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        $response->assertJson(['success' => false]);

        // Test payment without fee amount set
        $requestWithoutFee = RequestModel::factory()->create([
            'fee_amount' => 0,
        ]);

        $response = $this->post(route('requests.add-payment', $requestWithoutFee), [
            'amount' => 100.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        $response->assertJson(['success' => false]);

        // Test invalid payment method
        $response = $this->post(route('requests.add-payment', $this->request), [
            'amount' => 100.00,
            'payment_method' => 'invalid_method',
            'payment_date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('payment_method');
    }

    /**
     * Test payment cancellation
     * @test
     */
    public function can_cancel_payment_with_reason()
    {
        $payment = RequestPayment::factory()->create([
            'request_id' => $this->request->id,
            'amount' => 300.00,
            'status' => 'active',
        ]);

        $this->request->update([
            'paid_amount' => 300.00,
            'remaining_amount' => 700.00,
        ]);

        $this->actingAs($this->cashier);

        $response = $this->post(route('requests.cancel-payment', $payment), [
            'cancellation_reason' => 'خطأ في إدخال المبلغ',
        ]);

        $response->assertJson(['success' => true]);

        $payment->refresh();
        $this->assertEquals('cancelled', $payment->status);
        $this->assertEquals('خطأ في إدخال المبلغ', $payment->cancellation_reason);

        $this->request->refresh();
        $this->assertEquals(0.00, $this->request->paid_amount);
        $this->assertEquals(1000.00, $this->request->remaining_amount);
    }

    /**
     * Test multiple installments payment
     * @test
     */
    public function can_process_multiple_installments()
    {
        $this->actingAs($this->cashier);

        // First payment
        $this->post(route('requests.add-payment', $this->request), [
            'amount' => 200.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        // Second payment
        $this->post(route('requests.add-payment', $this->request), [
            'amount' => 300.00,
            'payment_method' => 'bank',
            'payment_date' => now()->toDateString(),
        ]);

        // Final payment
        $this->post(route('requests.add-payment', $this->request), [
            'amount' => 500.00,
            'payment_method' => 'check',
            'payment_date' => now()->toDateString(),
        ]);

        $this->request->refresh();
        $this->assertEquals(1000.00, $this->request->paid_amount);
        $this->assertEquals(0.00, $this->request->remaining_amount);

        // Check all payments exist
        $this->assertEquals(3, $this->request->payments()->count());
    }

    /**
     * Test craft license payment processing
     * @test
     */
    public function can_process_craft_license_payment()
    {
        $craftLicense = CraftLicense::factory()->create([
            'fee_amount' => 500.00,
            'paid_amount' => 0,
            'remaining_amount' => 500.00,
        ]);

        $this->actingAs($this->cashier);

        $paymentData = [
            'amount' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
            'receipt_number' => 'CRF-' . $this->faker->unique()->numberBetween(1000, 9999),
        ];

        $response = $this->post(route('craft-licenses.add-payment', $craftLicense), $paymentData);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('payments', [
            'craft_license_id' => $craftLicense->id,
            'amount' => 500.00,
            'status' => 'active',
        ]);

        $craftLicense->refresh();
        $this->assertEquals(500.00, $craftLicense->paid_amount);
        $this->assertEquals(0.00, $craftLicense->remaining_amount);
    }

    /**
     * Test payment reporting
     * @test
     */
    public function can_generate_payment_reports()
    {
        // Create multiple payments
        RequestPayment::factory()->count(5)->create([
            'status' => 'active',
            'payment_method' => 'cash',
            'created_at' => now()->subDays(2),
        ]);

        RequestPayment::factory()->count(3)->create([
            'status' => 'cancelled',
            'payment_method' => 'bank',
            'created_at' => now()->subDays(1),
        ]);

        $this->actingAs($this->admin);

        // Test daily report
        $response = $this->get(route('payments.report', [
            'period' => 'daily',
            'date' => now()->toDateString(),
        ]));

        $response->assertSuccessful();

        // Test method breakdown
        $response = $this->get(route('payments.report', [
            'type' => 'method_breakdown',
        ]));

        $response->assertJsonStructure([
            'cash' => ['total', 'count'],
            'bank' => ['total', 'count'],
            'check' => ['total', 'count'],
        ]);
    }

    /**
     * Test payment permissions
     * @test
     */
    public function payment_permissions_work_correctly()
    {
        $unauthorizedUser = User::factory()->create();
        $this->actingAs($unauthorizedUser);

        $response = $this->post(route('requests.add-payment', $this->request), [
            'amount' => 100.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        $response->assertForbidden();

        // Test authorized user
        $this->actingAs($this->cashier);

        $response = $this->post(route('requests.add-payment', $this->request), [
            'amount' => 100.00,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ]);

        $response->assertJson(['success' => true]);
    }

    /**
     * Test payment receipt generation
     * @test
     */
    public function can_generate_payment_receipt()
    {
        $payment = RequestPayment::factory()->create([
            'request_id' => $this->request->id,
            'amount' => 250.00,
            'receipt_number' => 'RCP-1234',
            'status' => 'active',
        ]);

        $this->actingAs($this->cashier);

        $response = $this->get(route('payments.receipt', $payment));

        $response->assertSuccessful()
            ->assertViewIs('payments.receipt')
            ->assertViewHas('payment', $payment);
    }
}

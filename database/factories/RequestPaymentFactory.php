<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\RequestPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestPaymentFactory extends Factory
{
    protected $model = RequestPayment::class;

    public function definition(): array
    {
        return [
            'request_id' => Request::factory(),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'payment_method' => $this->faker->randomElement(['cash', 'bank', 'check']),
            'payment_date' => now(),
            'receipt_number' => $this->faker->unique()->numerify('REC-########'),
            'status' => 'active',
            'created_by' => User::factory(),
        ];
    }
}

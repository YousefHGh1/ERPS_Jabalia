<?php

namespace Database\Factories;

use App\Models\Mokalaf;
use App\Models\Request;
use App\Models\RequestStatus;
use App\Models\RequestType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestFactory extends Factory
{
    protected $model = Request::class;

    public function definition(): array
    {
        return [
            'text' => $this->faker->sentence(),
            'request_type_id' => RequestType::factory(),
            'status_id' => RequestStatus::factory(),
            'mokalaf_id' => Mokalaf::factory(),
            'common_name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'mobile' => $this->faker->numerify('059#######'),
            'fee_amount' => $this->faker->randomFloat(2, 100, 1000),
            'paid_amount' => 0,
            'remaining_amount' => $this->faker->randomFloat(2, 100, 1000),
            'is_urgent' => $this->faker->boolean(20),
            'created_by' => User::factory(),
        ];
    }
}

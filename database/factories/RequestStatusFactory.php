<?php

namespace Database\Factories;

use App\Models\RequestStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestStatusFactory extends Factory
{
    protected $model = RequestStatus::class;

    public function definition(): array
    {
        $statuses = ['قيد الانتظار', 'مقبول', 'مرفوض', 'معلق', 'منتهي'];
        
        return [
            'name' => $this->faker->unique()->randomElement($statuses),
            'color' => $this->faker->hexColor(),
            'description' => $this->faker->sentence(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Mokalaf;
use Illuminate\Database\Eloquent\Factories\Factory;

class MokalafFactory extends Factory
{
    protected $model = Mokalaf::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'identity_number' => $this->faker->unique()->numerify('##########'),
            'mobile' => $this->faker->numerify('059#######'),
            'address' => $this->faker->address(),
        ];
    }
}

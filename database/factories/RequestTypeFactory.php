<?php

namespace Database\Factories;

use App\Models\RequestType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestTypeFactory extends Factory
{
    protected $model = RequestType::class;

    public function definition(): array
    {
        $types = ['رخصة حرفة', 'أذن بناء', 'ترخيص نشاط', 'استخراج شهادة', 'تقرير بلدي'];
        
        return [
            'name' => $this->faker->unique()->randomElement($types),
            'description' => $this->faker->sentence(),
        ];
    }
}

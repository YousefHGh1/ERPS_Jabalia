<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $category = \App\Models\Category::first() ?? \App\Models\Category::create([
            'name' => 'General',
            'start_degree' => 'A',
            'end_degree' => 'Z',
            'bonus_percent' => '0',
        ]);

        $job = \App\Models\Job::first() ?? \App\Models\Job::create([
            'name' => 'General Worker',
            'category_id' => $category->id,
        ]);

        return [
            'czn_id' => $this->faker->unique()->numerify('CZ-#####'),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'status' => 'active',
            'address' => $this->faker->address(),
            'mobile' => $this->faker->numerify('059#######'),
            'birthdate' => $this->faker->date(),
            'birthplace' => $this->faker->city(),
            'salary' => $this->faker->numberBetween(2000, 10000),
            'department_id' => null,
            'section_id' => null,
            'branch_id' => null,
            'job_id' => $job->id,
        ];
    }
}

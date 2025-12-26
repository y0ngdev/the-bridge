<?php

namespace Database\Factories;

use App\Models\Alumnus;
use App\Models\Department;
use App\Models\Tenure;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlumnusFactory extends Factory
{
    protected $model = Alumnus::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phones' => [$this->faker->phoneNumber()],
            'department_id' => Department::query()->inRandomOrder()->first()?->id ?? Department::factory(),
            'gender' => $this->faker->randomElement(['M', 'F']),
            'state' => $this->faker->randomElement(\App\Enums\NigerianState::cases()),
            'unit' => $this->faker->randomElement(\App\Enums\Unit::cases()),
            'address' => $this->faker->address(),
            'is_futa_staff' => $this->faker->boolean(20),
            'birth_date' => $this->faker->date(),
            'tenure_id' => Tenure::query()->inRandomOrder()->first()?->id ?? Tenure::factory(),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RedemptionWeekSession>
 */
class RedemptionWeekSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = $this->faker->numberBetween(2020, 2030);
        $startDate = $this->faker->dateTimeBetween("{$year}-01-01", "{$year}-12-31");

        return [
            'name' => "RW'".substr((string) $year, -2),
            'year' => $year,
            'start_date' => $startDate,
            'end_date' => (clone $startDate)->modify('+7 days'),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => false,
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expenses>
 */
class ExpensesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => rand(1, 5),
            'name' => $this->faker->unique()->word(),
            'amount' => $this->faker->numberBetween(100, 1000),
            'date' => $this->faker->date(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'students_id' => Student::inRandomOrder()->first()->id,
            'amount' => $this->faker->numberBetween(1000, 5000),
            'payment_date' => $this->faker->date(),
            'processed_by' => 1,
            'paid_month' => $this->faker->monthName(),
            'paid_year' => "2024",
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
        ];
    }
}

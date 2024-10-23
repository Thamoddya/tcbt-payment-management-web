<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $year = date('Y');
        $random = mt_rand(1000000000, 9999999999);
        $tcbt_student_number = "TCBT$year-$random";

        return [
            'tcbt_student_number' => $tcbt_student_number,
            'name' => $this->faker->name(),
            'contact_no' => '070' . $this->faker->numberBetween(1000000, 9999999),
            'grade' => $this->faker->randomElement(['9', '10', '11', '12']),
            'school' => $this->faker->randomElement(['TCC', 'ABC High', 'XYZ School']),
            'address' => $this->faker->address(),
            'parent_contact_no' => '071' . $this->faker->numberBetween(1000000, 9999999),
            'parent_name' => $this->faker->name(),
            'status' => $this->faker->randomElement([0, 1]),
        ];
    }
}

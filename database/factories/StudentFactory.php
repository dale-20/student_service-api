<?php

namespace Database\Factories;

use App\Enums\StudentStatus;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Student> */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'program_id' => Program::factory(),
            'student_number' => fake()->unique()->numerify('20##-#####'),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional(0.7)->firstName(),
            'last_name' => fake()->lastName(),
            'suffix' => fake()->optional(0.05)->randomElement(['Jr.', 'III']),
            'birth_date' => fake()->optional()->dateTimeBetween('-25 years', '-17 years'),
            'email' => fake()->optional(0.8)->safeEmail(),
            'contact_number' => fake()->optional()->numerify('09#########'),
            'address' => fake()->optional()->address(),
            'year_level' => fake()->numberBetween(1, 5),
            'status' => StudentStatus::Active,
        ];
    }
}

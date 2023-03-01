<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $f_name = fake()->firstName();
        $l_name = fake()->lastName();
        return [
            'first_name'        => $f_name,
            'last_name'         => $l_name,
            'email'             => fake()->unique()->safeEmail(),
            'phone_number'      => fake()->unique()->numerify('##########'),
            'email_verified_at' => now(),
            //'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'status'            =>1,
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
            'token'             => Str::random(12)
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

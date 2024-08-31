<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UsersFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prenom' => fake()->lastName(),
            'nom' => fake()->firstName(),
            'login' =>fake()->userName(),
            'role' => 'Boutiquier', // par défaut, le rôle est Boutiquier
            'mail' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
    /**
     * Assigne role 
     */
    public function assignRole(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'ADMIN',
            ];
        });
    }

    public function boutiquier()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'Boutiquier',
            ];
        });
    }

   
}

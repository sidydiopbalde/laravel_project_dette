<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\UsersFactory as UsersFactory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clients>
 */
class ClientsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'surnom' => fake()->lastName(),
            'adresse' => fake()->firstName(),
            'telephone' => fake()->unique()->phoneNumber(),
            'user_id' => null, //useless because we create client without user_id, but for the sake of this example, we'll add a user_id here. It should be null or a real user id.  //faker->numberBetween(1, 100), //replace with real user id if you have one.  //faker->unique()->numberBetween(1, 100), //replace with real user id if you have one.
        ];

    }
    public function withUser()
    {
        return $this->state(function (array $attributes) {
            $user = UsersFactory::factory()->create();
            return [
                'user_id' => $user->id,
            ];
        });
    }
    //create client without user
    public function withoutuser()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => null,
            ];
        });
    }
}

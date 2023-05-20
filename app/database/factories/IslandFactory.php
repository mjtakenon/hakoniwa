<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class IslandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'name' => $this->faker->name,
            'owner_name' => $this->faker->name,
        ];
    }

    public function setUser(User $user): Factory {
        return $this->state(function () use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }
}

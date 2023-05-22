<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class TurnFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'turn' => $this->faker->randomNumber(),
            'next_turn_scheduled_at' => now(),
        ];
    }

    public function setTurn(int $turn): Factory
    {
        return $this->state(function () use ($turn) {
            return [
                'turn' => $turn,
            ];
        });
    }
}

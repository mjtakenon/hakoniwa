<?php

namespace Database\Factories;

use App\Models\Island;
use App\Models\Turn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class IslandStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'turn_id' => $this->faker->randomNumber(),
            'island_id' => $this->faker->randomNumber(),
            'development_points' => $this->faker->randomNumber(),
            'funds' => $this->faker->randomNumber(),
            'foods' => $this->faker->randomNumber(),
            'resources' => $this->faker->randomNumber(),
            'population' => $this->faker->randomNumber(),
            'funds_production_capacity' => $this->faker->randomNumber(),
            'foods_production_capacity' => $this->faker->randomNumber(),
            'resources_production_capacity' => $this->faker->randomNumber(),
            'maintenance_number_of_people' => $this->faker->randomNumber(),
            'environment' => 'best',
            'area' => $this->faker->randomNumber(),
            'abandoned_turn' => $this->faker->randomNumber(),
        ];
    }

    public function setTurn(Turn $turn): Factory
    {
        return $this->state(function () use ($turn) {
            return [
                'turn_id' => $turn->id,
            ];
        });
    }

    public function setIsland(Island $island): Factory
    {
        return $this->state(function () use ($island) {
            return [
                'island_id' => $island->id,
            ];
        });
    }

    public function setDevelopmentPoints(int $developmentPoints): Factory
    {
        return $this->state(function () use ($developmentPoints) {
            return [
                'development_points' => $developmentPoints,
            ];
        });
    }

    public function setFunds(int $funds): Factory
    {
        return $this->state(function () use ($funds) {
            return [
                'funds' => $funds,
            ];
        });
    }
}

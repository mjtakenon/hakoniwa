<?php

namespace Database\Factories;

use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\Turn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class IslandTerrainFactory extends Factory
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
            'terrain' => Terrain::create()->init()->toJson(true),
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

    public function setTerrain(Terrain $terrain): Factory
    {
        return $this->state(function () use ($terrain) {
            return [
                'terrain' => $terrain->toJson(true),
            ];
        });
    }
}

<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Entity\Log\LogRow\IslandFoundLog;
use App\Entity\Plan\Plans;
use App\Entity\Status\Status;
use App\Entity\Terrain\Terrain;
use App\Models\Island;
use App\Models\IslandLog;
use App\Models\IslandPlan;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Models\Turn;
use App\Models\User;
use Illuminate\Database\Seeder;

class RegisterIslandSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create();

        $island = Island::create([
            'user_id' => $user->id,
            'name' => fake()->name,
            'owner_name' => fake()->name,
        ]);

        $turn = Turn::latest()->firstOrFail();

        $terrain = IslandTerrain::create([
            'island_id' => $island->id,
            'turn_id' => $turn->id,
            'terrain' => Terrain::create()->init()->toJson(true),
        ]);

        $status = Status::create()->init($terrain->toEntity());

        IslandStatus::create([
            'island_id' => $island->id,
            'turn_id' => $turn->id,
            'development_points' => $status->getDevelopmentPoints(),
            'funds' => $status->getFunds(),
            'foods' => $status->getFoods(),
            'resources' => $status->getResources(),
            'population' => $status->getPopulation(),
            'funds_production_capacity' => $status->getFundsProductionCapacity(),
            'foods_production_capacity' => $status->getFoodsProductionCapacity(),
            'resources_production_capacity' => $status->getResourcesProductionCapacity(),
            'environment' => $status->getEnvironment(),
            'area' => $status->getArea(),
            'abandoned_turn' => $status->getAbandonedTurn(),
        ]);

        IslandPlan::create([
            'island_id' => $island->id,
            'turn_id' => $turn->id,
            'plan' => Plans::init()->toJson(),
        ]);

        $islandFoundLog = new IslandFoundLog($island);

        IslandLog::create([
            'island_id' => $island->id,
            'turn_id' => $turn->id,
            'log' => $islandFoundLog->generate(),
            'visibility' => $islandFoundLog->getVisibility(),
        ]);
    }
}

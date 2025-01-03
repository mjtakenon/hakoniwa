<?php

namespace App\Http\Controllers\Api\Islands;

use App\Entity\Cell\Ship\Battleship;
use App\Entity\Cell\Ship\Submarine;
use App\Http\Controllers\Controller;
use App\Http\Traits\WebApi;
use App\Models\Island;
use App\Models\IslandHistory;
use App\Models\IslandStatus;
use App\Models\IslandTerrain;
use App\Models\Turn;

class DetailController extends Controller
{
    use WebApi;

    public function get(int $islandId): \Illuminate\Http\JsonResponse
    {
        /** @var Island $island */
        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            return $this->notFound();
        }

        $turn = Turn::latest()->firstOrFail();
        $islandComment = $island->islandComments()->first();
        $islandTerrain = $island->islandTerrains()->where('turn_id', $turn->id)->firstOrFail();

        return $this->ok([
            'island' => [
                'id' => $island->id,
                'name' => $island->name,
                'owner_name' => $island->owner_name,
                'comment' => $islandComment->comment ?? null,
                'terrains' => $islandTerrain->toEntity()->toArray(false, true),
            ]
        ]);
    }

    public function patch(int $islandId): \Illuminate\Http\JsonResponse
    {
        $validator = \Validator::make(\Request::all(), [
            'name' => 'string|nullable|required_without:owner_name',
            'owner_name' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return $this->badRequest();
        }

        if (!\HakoniwaService::isIslandRegistered() || \Auth::user()->island->id !== $islandId) {
            return $this->forbidden();
        }

        $validated = $validator->safe()->collect();

        return \DB::transaction(function () use ($islandId, $validated) {

            $island = Island::find($islandId);

            if (is_null($island) || !is_null($island->deleted_at)) {
                return $this->notFound();
            }

            $turn = Turn::latest()->firstOrFail();
            /** @var IslandStatus $islandStatus */
            $islandStatus = $island->islandStatuses()->where('turn_id', $turn->id)->firstOrFail();
            /** @var IslandTerrain $islandTerrain */
            $islandTerrain = $island->islandTerrains()->where('turn_id', $turn->id)->firstOrFail();

            $status = $islandStatus->toEntity();
            $terrain = $islandTerrain->toEntity();

            $name  = $validated->get('name');
            $ownerName  = $validated->get('owner_name');

            if (Island::where('name', $name)->where('id', '!=', $island->id)->exists()) {
                return $this->badRequest([
                    'code' => 'island_name_duplicated'
                ]);
            }

            if (Island::where('owner_name', $ownerName)->where('id', '!=', $island->id)->exists()) {
                return $this->badRequest([
                    'code' => 'owner_name_duplicated'
                ]);
            }

            if ($status->getFunds() < config('app.hakoniwa.change_island_name_price')) {
                return $this->badRequest([
                    'code' => 'lack_of_funds'
                ]);
            }
            $updated = false;
            $islandHistory = IslandHistory::createFromIsland($island);

            if (!is_null($name) && $island->name !== $name) {
                $island->name = $name;
                $updated = true;
            }

            if (!is_null($ownerName) && $island->owner_name !== $ownerName) {
                $island->owner_name = $ownerName;
                $updated = true;
            }

            if ($updated) {
                $islandHistory->save();
                $island->save();

                $status->setFunds($status->getFunds() - config('app.hakoniwa.change_island_name_price'));
                $islandStatus->funds = $status->getFunds();
                $islandStatus->save();

                $islandTerrain->terrain = $terrain->changeIslandName($island)->toJson(true);
                $islandTerrain->save();

                return $this->ok([
                    'island' => [
                        'id' => $island->id,
                        'name' => $island->name,
                        'owner_name' => $island->owner_name,
                    ]
                ]);
            } else {
                return $this->badRequest([
                    'code' => 'not_changed'
                ]);
            }
        });
    }

}

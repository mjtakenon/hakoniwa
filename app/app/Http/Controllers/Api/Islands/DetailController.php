<?php

namespace App\Http\Controllers\Api\Islands;

use App\Http\Controllers\Api\WebApi;
use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\IslandHistory;
use App\Models\Turn;

class DetailController extends Controller
{
    use WebApi;

    const CHANGE_ISLAND_NAME_PRICE = 1000;

    public function get(int $islandId): \Illuminate\Http\JsonResponse
    {
        /** @var Island $island */
        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            return $this->notFound();
        }

        $turn = Turn::latest()->firstOrFail();
        $islandTerrain = $island->islandTerrains->where('turn_id', $turn->id)->firstOrFail();

        return $this->ok([
            'island' => [
                'id' => $island->id,
                'name' => $island->name,
                'owner_name' => $island->owner_name,
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

        $island = Island::find($islandId);

        if (is_null($island) || !is_null($island->deleted_at)) {
            return $this->notFound();
        }

        $turn = Turn::latest()->firstOrFail();
        $validated = $validator->safe()->collect();
        $islandStatus = $island->islandStatuses->where('turn_id', $turn->id)->firstOrFail();

        return \DB::transaction(function () use ($island, $islandStatus, $validated) {

            $status = $islandStatus->toEntity();
            if ($status->getFunds() < self::CHANGE_ISLAND_NAME_PRICE) {
                return $this->badRequest([
                    'code' => 'lack_of_funds'
                ]);
            }

            $name  = $validated->get('name');
            $ownerName  = $validated->get('owner_name');
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
                $status->setFunds($status->getFunds() - self::CHANGE_ISLAND_NAME_PRICE);
                $islandStatus->funds = $status->getFunds();
                $islandStatus->save();

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

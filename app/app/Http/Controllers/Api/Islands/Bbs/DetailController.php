<?php

namespace App\Http\Controllers\Api\Islands\Bbs;

use App\Http\Controllers\Controller;
use App\Http\Traits\WebApi;
use App\Models\Island;
use App\Models\IslandBbs;

class DetailController extends Controller
{
    use WebApi;

    public function delete(int $islandId, int $bbsId): \Illuminate\Http\JsonResponse
    {
        if (!\HakoniwaService::isIslandRegistered()) {
            return $this->forbidden();
        }

        return \DB::transaction(function () use ($islandId, $bbsId) {

            $island = Island::find($islandId);

            if (is_null($island) || !is_null($island->deleted_at)) {
                return $this->notFound();
            }

            $islandBbs = IslandBbs::find($bbsId);

            if (is_null($islandBbs)) {
                return $this->notFound();
            }

            $user = \Auth::user();
            /** @var Island $commenterIsland */
            $userIsland = $user->island;

            if ($islandBbs->commenter_user_id !== $user->id) {
                return $this->forbidden();
            }

            $islandBbs->delete();

            $islandBbses = IslandBbs::where('island_id', $islandId)
                ->withTrashed()
                ->orderByDesc('id')
                ->limit(config('app.hakoniwa.default_show_bbs_comments'))
                ->with(['commenterIsland', 'turn'])
                ->get();

            return response()->json([
                    'bbs' => $islandBbses->map(function ($islandBbs) use ($user, $userIsland) {
                        /** @var IslandBbs $islandBbs */
                        return $islandBbs->toViewArray($islandBbs->turn, $islandBbs->commenterIsland, $user, $userIsland);
                    })]
            );
        });
    }

}

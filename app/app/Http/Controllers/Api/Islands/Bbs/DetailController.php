<?php

namespace App\Http\Controllers\Api\Islands\Bbs;

use App\Http\Controllers\Controller;
use App\Http\Traits\WebApi;
use App\Models\Island;
use App\Models\IslandBbs;

class DetailController extends Controller
{
    use WebApi;

    const DEFAULT_SHOW_BBS_COMMENTS = 10;

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

            if ($islandBbs->commenter_user_id !== $user->id) {
                return $this->forbidden();
            }

            $islandBbs->delete();

            $islandBbses = IslandBbs::where('island_id', $islandId)
                ->withTrashed()
                ->orderByDesc('id')
                ->limit(self::DEFAULT_SHOW_BBS_COMMENTS)
                ->with(['island', 'commenterUser', 'commenterIsland', 'turn'])
                ->get();

            return response()->json([
                    'bbs' => $islandBbses->map(function ($islandBbs) use ($user) {
                        /** @var IslandBbs $islandBbs */
                        return $islandBbs->toViewArray($user, $user->island);
                    })]
            );
        });
    }

}

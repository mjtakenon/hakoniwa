<?php

namespace App\Http\Controllers\Api\Islands\Bbs;

use App\Http\Controllers\Controller;
use App\Http\Traits\WebApi;
use App\Models\Island;
use App\Models\IslandBbs;
use App\Models\IslandStatus;
use App\Models\Turn;
use App\Models\User;

class IndexController extends Controller
{
    use WebApi;

    public function post(int $islandId): \Illuminate\Http\JsonResponse
    {
        $validator = \Validator::make(\Request::all(), [
            'comment' => 'string|required',
            'visibility' => 'required|in:public,private',
        ]);

        if ($validator->fails()) {
            return $this->badRequest();
        }

        if (!\HakoniwaService::isIslandRegistered()) {
            return $this->forbidden();
        }

        $validated = $validator->safe()->collect();

        return \DB::transaction(function () use ($islandId, $validated) {

            $island = Island::find($islandId);

            if (is_null($island) || !is_null($island->deleted_at)) {
                return $this->notFound();
            }

            $turn = Turn::latest()->firstOrFail();

            /** @var User $user */
            $commenterUser = \Auth::user();

            /** @var Island $commenterIsland */
            $commenterIsland = $commenterUser->island;

            /** @var IslandStatus $commenterIslandStatus */
            $commenterIslandStatus = $commenterIsland->islandStatuses->where('turn_id', $turn->id)->firstOrFail();

            $comment = $validated->get('comment');
            $visibility = $validated->get('visibility');

            if ($visibility === IslandBbs::VISIBILITY_PRIVATE) {
                if ($commenterIsland->id === $islandId) {
                    return $this->badRequest();
                }

                if ($commenterIslandStatus->funds >= config('app.hakoniwa.private_post_price')) {
                    $commenterIslandStatus->funds -= config('app.hakoniwa.private_post_price');
                    $commenterIslandStatus->save();
                } else {
                    return $this->badRequest([
                        'code' => 'lack_of_funds'
                    ]);
                }
            }

            $islandBbs = new IslandBbs();
            $islandBbs->island_id = $island->id;
            $islandBbs->commenter_user_id = $commenterUser->id;
            $islandBbs->commenter_island_id = $commenterIsland->id;
            $islandBbs->turn_id = $turn->id;
            $islandBbs->comment = $comment;
            $islandBbs->visibility = $visibility;
            $islandBbs->save();

            $islandBbses = IslandBbs::where('island_id', $islandId)
                ->withTrashed()
                ->orderByDesc('id')
                ->limit(config('app.hakoniwa.default_show_bbs_comments'))
                ->with(['commenterIsland', 'turn'])
                ->get();

            return response()->json([
                'bbs' => $islandBbses->map(function ($islandBbs) use ($commenterUser, $commenterIsland) {
                    /** @var IslandBbs $islandBbs */
                    return $islandBbs->toViewArray($islandBbs->turn, $islandBbs->commenterIsland, $commenterUser, $commenterIsland);
                })]
            );
        });
    }

}

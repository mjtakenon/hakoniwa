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

    const PRIVATE_POST_PRICE = 1000;
    const DEFAULT_SHOW_BBS_COMMENTS = 10;

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
            $commenterIsland = $commenterUser->island()->firstOrFail();

            /** @var IslandStatus $commenterIslandStatus */
            $commenterIslandStatus = $commenterIsland->islandStatuses->where('turn_id', $turn->id)->firstOrFail();

            $comment = $validated->get('comment');
            $visibility = $validated->get('visibility');

            if ($visibility === IslandBbs::VISIBILITY_PRIVATE) {
                if ($commenterIslandStatus->funds >= self::PRIVATE_POST_PRICE) {
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
                ->limit(self::DEFAULT_SHOW_BBS_COMMENTS)
                ->with(['island', 'commenterUser', 'commenterIsland', 'turn'])
                ->get();

            return response()->json(
                $islandBbses->map(function ($islandBbs) use ($commenterUser) {
                    /** @var IslandBbs $islandBbs */
                    $row = [
                        'id' => $islandBbs->id,
                        'user_id' => $islandBbs->commenterUser->id,
                        'visibility' => $islandBbs->visibility,
                        'deleted' => !is_null($islandBbs->deleted_at),
                    ];

                    if (!is_null($islandBbs->deleted_at)) {
                        return $row;
                    }

                    if ($islandBbs->visibility === IslandBbs::VISIBILITY_PRIVATE) {
                        if ($islandBbs->island_id !== $commenterUser->id && $islandBbs->commenterIsland->id !== $commenterUser->id) {
                            return $row;
                        }
                    }

                    $row['turn'] = $islandBbs->turn->turn;
                    $row['comment'] = $islandBbs->comment;

                    if (!is_null($islandBbs->commenterIsland)) {
                        $row['island'] = [
                            'id' => $islandBbs->commenterIsland->id,
                            'name' => $islandBbs->commenterIsland->name,
                            'owner_name' => $islandBbs->commenterIsland->owner_name,
                        ];
                    }

                    return $row;
                })
            );
        });
    }

}

<?php

namespace App\Http\Controllers\Api\Islands;

use App\Http\Traits\WebApi;
use App\Models\Island;
use App\Models\IslandComment;

class CommentsController
{

    use WebApi;
    public function post(int $islandId): \Illuminate\Http\JsonResponse
    {
        $validator = \Validator::make(\Request::all(), [
            'comment' => 'string|nullable|max:128',
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

        $validated = $validator->safe()->collect();

        $comment = $validated->get('comment', '');
        $comment = preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $comment);
        if ($comment === '') {
            $comment = null;
        }

        return \DB::transaction(function () use ($island, $comment) {

            /** @var IslandComment $islandComment */
            $islandComment = $island->islandComments()->first();
            if (!is_null($islandComment)) {

                if ($islandComment->comment === $comment) {
                    return $this->ok(['comment' => $islandComment->comment]);
                }

                $island->islandComments->each(function ($islandComment) {
                    $islandComment->delete();
                });
            }
            $newIslandComment = new IslandComment();
            $newIslandComment->island_id = $island->id;
            $newIslandComment->comment = $comment;
            $newIslandComment->save();

            return $this->ok(['comment' => $newIslandComment->comment]);
        });
    }
}

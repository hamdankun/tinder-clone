<?php

namespace App\Repositories\Implementations;

use App\Models\Dislike;
use App\Repositories\Contracts\DislikeRepositoryContract;

class DislikeRepository implements DislikeRepositoryContract
{
    public function create(array $data)
    {
        return Dislike::create($data);
    }

    public function exists(int $fromUserId, int $toUserId): bool
    {
        return Dislike::where('from_user_id', $fromUserId)
            ->where('to_user_id', $toUserId)
            ->exists();
    }

    public function delete(int $fromUserId, int $toUserId): bool
    {
        return Dislike::where('from_user_id', $fromUserId)
            ->where('to_user_id', $toUserId)
            ->delete() > 0;
    }
}

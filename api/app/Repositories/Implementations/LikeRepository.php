<?php

namespace App\Repositories\Implementations;

use App\Models\Like;
use App\Repositories\Contracts\LikeRepositoryContract;

class LikeRepository implements LikeRepositoryContract
{
    public function create(array $data)
    {
        return Like::create($data);
    }

    public function exists(int $fromUserId, int $toUserId): bool
    {
        return Like::where('from_user_id', $fromUserId)
            ->where('to_user_id', $toUserId)
            ->exists();
    }

    public function countLikesReceived(int $userId): int
    {
        return Like::where('to_user_id', $userId)->count();
    }

    public function getLikedPeople(int $userId, int $page = 1, int $perPage = 10): array
    {
        $paginator = Like::where('from_user_id', $userId)
            ->with('toUser.pictures')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function delete(int $fromUserId, int $toUserId): bool
    {
        return Like::where('from_user_id', $fromUserId)
            ->where('to_user_id', $toUserId)
            ->delete() > 0;
    }
}

<?php

namespace App\Services;

use App\Repositories\Contracts\DislikeRepositoryContract;
use App\Repositories\Contracts\LikeRepositoryContract;
use App\Exceptions\UserAlreadyDislikedException;

class DislikeService
{
    public function __construct(
        private DislikeRepositoryContract $dislikeRepository,
        private LikeRepositoryContract $likeRepository,
    ) {}

    /**
     * Dislike/pass on a user
     * 
     * @param int $fromUserId
     * @param int $toUserId
     * @return array
     * @throws UserAlreadyDislikedException
     */
    public function dislikeUser(int $fromUserId, int $toUserId): array
    {
        // Check if already disliked
        if ($this->dislikeRepository->exists($fromUserId, $toUserId)) {
            throw new UserAlreadyDislikedException();
        }

        // If user previously liked, remove the like (user changed their mind)
        if ($this->likeRepository->exists($fromUserId, $toUserId)) {
            $this->likeRepository->delete($fromUserId, $toUserId);
        }

        // Create dislike record
        $this->dislikeRepository->create([
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
        ]);

        return ['success' => true];
    }

    /**
     * Remove a dislike
     * 
     * @param int $fromUserId
     * @param int $toUserId
     * @return bool
     */
    public function undislike(int $fromUserId, int $toUserId): bool
    {
        return $this->dislikeRepository->delete($fromUserId, $toUserId);
    }
}

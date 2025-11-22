<?php

namespace App\Services;

use App\Repositories\Contracts\LikeRepositoryContract;
use App\Repositories\Contracts\DislikeRepositoryContract;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Events\UserLiked;
use App\Events\UserMatched;
use App\Exceptions\UserAlreadyLikedException;
use App\Jobs\SendLikeThresholdNotification;

class LikeService
{
    public function __construct(
        private LikeRepositoryContract $likeRepository,
        private DislikeRepositoryContract $dislikeRepository,
        private UserRepositoryContract $userRepository,
    ) {}

    public function likeUser(int $fromUserId, int $toUserId): array
    {
        // Check if already liked
        if ($this->likeRepository->exists($fromUserId, $toUserId)) {
            throw new UserAlreadyLikedException('User already liked');
        }

        // If user previously disliked, remove the dislike (user changed their mind)
        if ($this->dislikeRepository->exists($fromUserId, $toUserId)) {
            $this->dislikeRepository->delete($fromUserId, $toUserId);
        }

        // Create like record
        $this->likeRepository->create([
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
        ]);

        // Fire event for observers
        UserLiked::dispatch($fromUserId, $toUserId);

        // Check for mutual like (match)
        $mutualLike = $this->likeRepository->exists($toUserId, $fromUserId);
        if ($mutualLike) {
            UserMatched::dispatch($fromUserId, $toUserId);
            return ['success' => true, 'matched' => true];
        }

        // Check if threshold reached (50+ likes)
        $likeCount = $this->likeRepository->countLikesReceived($toUserId);
        if ($likeCount >= 50) {
            SendLikeThresholdNotification::dispatch($toUserId);
        }

        return ['success' => true, 'matched' => false];
    }

    public function getLikedPeople(int $userId, int $page = 1, int $perPage = 10): array
    {
        return $this->likeRepository->getLikedPeople($userId, $page, $perPage);
    }

    public function unlike(int $fromUserId, int $toUserId): bool
    {
        return $this->likeRepository->delete($fromUserId, $toUserId);
    }
}

<?php

namespace App\Repositories\Contracts;

interface LikeRepositoryContract
{
    public function create(array $data);
    
    public function exists(int $fromUserId, int $toUserId): bool;
    
    public function countLikesReceived(int $userId): int;
    
    public function getLikedPeople(int $userId, int $page = 1, int $perPage = 10): array;
    
    public function delete(int $fromUserId, int $toUserId): bool;
}

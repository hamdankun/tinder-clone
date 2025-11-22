<?php

namespace App\Repositories\Contracts;

interface DislikeRepositoryContract
{
    public function create(array $data);
    public function exists(int $fromUserId, int $toUserId): bool;
    public function delete(int $fromUserId, int $toUserId): bool;
}

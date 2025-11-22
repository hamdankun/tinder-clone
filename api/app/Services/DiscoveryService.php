<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryContract;
use App\DTOs\DiscoveryFilterDTO;

class DiscoveryService
{
    public function __construct(
        private UserRepositoryContract $userRepository,
    ) {}

    public function getRecommendedPeople(int $userId, int $page = 1, int $perPage = 10): array
    {
        return $this->userRepository->getRecommendedPeople($userId, $page, $perPage);
    }

    public function getRecommendedPeopleWithFilters(int $userId, DiscoveryFilterDTO $filters, int $page = 1, int $perPage = 10): array
    {
        // TODO: Implement filtering logic using Strategy pattern
        return $this->getRecommendedPeople($userId, $page, $perPage);
    }

    public function getUserById(int $userId)
    {
        return $this->userRepository->findById($userId);
    }
}

<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryContract
{
    public function findById(int $id): ?User;
    
    public function findByEmail(string $email): ?User;
    
    public function getRecommendedPeople(int $userId, int $page = 1, int $perPage = 10): array;
    
    public function create(array $data): User;
    
    public function update(int $id, array $data): User;
    
    public function delete(int $id): bool;
}

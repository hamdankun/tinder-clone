<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryContract;
use App\DTOs\UserDTO;
use App\Factories\UserFactory;
use App\Models\User;

class UserService
{
    public function __construct(
        private UserRepositoryContract $userRepository,
    ) {}

    public function registerUser(UserDTO $userDTO): User
    {
        $user = UserFactory::create($userDTO);
        return $user;
    }

    public function getUserById(int $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function updateUserProfile(int $userId, array $data): User
    {
        return $this->userRepository->update($userId, $data);
    }

    public function deleteUser(int $userId): bool
    {
        return $this->userRepository->delete($userId);
    }
}

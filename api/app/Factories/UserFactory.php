<?php

namespace App\Factories;

use App\Models\User;
use App\DTOs\UserDTO;

class UserFactory
{
    public static function create(UserDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => bcrypt($dto->password),
            'age' => $dto->age,
            'location' => $dto->location,
            'bio' => $dto->bio,
        ]);
    }

    public static function fromArray(array $data): User
    {
        return User::create($data);
    }
}

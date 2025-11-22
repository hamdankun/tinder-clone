<?php

namespace App\Services\Seeders;

use App\DTOs\UserDTO;
use App\Factories\UserFactory;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * User Seeder Service
 *
 * Handles user creation for database seeding
 * Uses DTO and Factory patterns for clean separation of concerns
 */
class UserSeederService
{
    /**
     * Create a seed user with generated data
     *
     * @param int $index Unique index for generating unique emails
     * @return User
     */
    public function createSeedUser(int $index): User
    {
        $userDto = $this->generateUserDTO($index);

        return UserFactory::create($userDto);
    }

    /**
     * Generate UserDTO from random seed data
     *
     * @param int $index Unique index for generating unique emails
     * @return UserDTO
     */
    private function generateUserDTO(int $index): UserDTO
    {
        $firstName = SeederDataProvider::getRandomItem(SeederDataProvider::getFirstNames());
        $lastName = SeederDataProvider::getRandomItem(SeederDataProvider::getLastNames());
        $age = SeederDataProvider::getRandomAge();
        $location = SeederDataProvider::getRandomItem(SeederDataProvider::getLocations());
        $bio = SeederDataProvider::getRandomItem(SeederDataProvider::getBios());

        $email = Str::slug($firstName . '.' . $lastName . $index) . '@example.com';

        return new UserDTO(
            name: "{$firstName} {$lastName}",
            email: $email,
            password: 'password123',
            age: $age,
            location: $location,
            bio: $bio,
        );
    }
}

<?php

namespace App\DTOs;

class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public int $age,
        public string $location,
        public ?string $bio = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            age: $data['age'],
            location: $data['location'],
            bio: $data['bio'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'age' => $this->age,
            'location' => $this->location,
            'bio' => $this->bio,
        ];
    }
}

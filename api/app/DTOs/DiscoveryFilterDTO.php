<?php

namespace App\DTOs;

class DiscoveryFilterDTO
{
    public function __construct(
        public ?int $minAge = null,
        public ?int $maxAge = null,
        public ?string $location = null,
        public int $page = 1,
        public int $perPage = 10,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            minAge: $data['min_age'] ?? null,
            maxAge: $data['max_age'] ?? null,
            location: $data['location'] ?? null,
            page: $data['page'] ?? 1,
            perPage: $data['per_page'] ?? 10,
        );
    }
}

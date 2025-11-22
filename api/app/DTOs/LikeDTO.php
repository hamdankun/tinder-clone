<?php

namespace App\DTOs;

class LikeDTO
{
    public function __construct(
        public int $fromUserId,
        public int $toUserId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            fromUserId: $data['from_user_id'],
            toUserId: $data['to_user_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'from_user_id' => $this->fromUserId,
            'to_user_id' => $this->toUserId,
        ];
    }
}

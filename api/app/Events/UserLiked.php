<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLiked
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $fromUserId,
        public int $toUserId,
    ) {}
}

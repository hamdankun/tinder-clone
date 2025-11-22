<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserMatched
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $userId1,
        public int $userId2,
    ) {}
}

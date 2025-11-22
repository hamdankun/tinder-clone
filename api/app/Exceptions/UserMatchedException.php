<?php

namespace App\Exceptions;

use Exception;

class UserMatchedException extends Exception
{
    public function __construct(string $message = 'Users already matched')
    {
        parent::__construct($message);
    }

    public function render()
    {
        return response()->json([
            'error' => $this->message,
        ], 409);
    }
}

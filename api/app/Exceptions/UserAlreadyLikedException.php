<?php

namespace App\Exceptions;

use Exception;

class UserAlreadyLikedException extends Exception
{
    public function __construct(string $message = 'User already liked')
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

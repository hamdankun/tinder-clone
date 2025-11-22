<?php

namespace App\Exceptions;

use Exception;

class InvalidUserException extends Exception
{
    public function __construct(string $message = 'Invalid user')
    {
        parent::__construct($message);
    }

    public function render()
    {
        return response()->json([
            'error' => $this->message,
        ], 400);
    }
}

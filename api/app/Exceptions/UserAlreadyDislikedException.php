<?php

namespace App\Exceptions;

use Exception;

class UserAlreadyDislikedException extends Exception
{
    public function __construct()
    {
        parent::__construct('User already disliked');
    }
}

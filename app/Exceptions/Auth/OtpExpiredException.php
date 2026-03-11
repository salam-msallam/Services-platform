<?php

namespace App\Exceptions\Auth;

use Exception;

class OtpExpiredException extends Exception
{
    public function __construct(string $message = 'OTP code has expired.')
    {
        parent::__construct($message);
    }
}

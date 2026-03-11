<?php

namespace App\Exceptions\Auth;

use Exception;

class InvalidOtpException extends Exception
{
    public function __construct(string $message = 'Invalid or expired OTP code.')
    {
        parent::__construct($message);
    }
}

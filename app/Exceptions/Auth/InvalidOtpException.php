<?php

namespace App\Exceptions\Auth;

use Exception;

class InvalidOtpException extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? __('api.invalid_otp'));
    }
}

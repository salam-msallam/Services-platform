<?php

declare(strict_types=1);

namespace App\Exceptions\BusinessAccount;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BusinessAccountActivityTypeAlreadyExistsException extends Exception
{
    public function render(Request $request): ?Response
    {
        if (! $request->expectsJson()) {
            return null;
        }

        return ApiResponse::error(
            $this->getMessage(),
            [],
            422
        );
    }
}


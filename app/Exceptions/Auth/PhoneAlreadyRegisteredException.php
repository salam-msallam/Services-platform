<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PhoneAlreadyRegisteredException extends Exception
{
    public function render(Request $request): ?Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $this->getMessage(),
            ], 409);
        }

        return null;
    }
}

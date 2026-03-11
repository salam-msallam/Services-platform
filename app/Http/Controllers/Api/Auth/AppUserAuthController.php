<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\AppUserRequestOtpRequest;
use App\Http\Requests\Auth\AppUserVerifyOtpRequest;
use App\Http\Resources\Auth\AppUserAuthResource;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AppUserAuthService;
use Illuminate\Http\JsonResponse;

class AppUserAuthController
{
    public function __construct( protected AppUserAuthService $appUserAuthService) {}

    public function requestOtp(AppUserRequestOtpRequest $request): JsonResponse
    {
        $this->appUserAuthService->requestOtp(
            $request->validated('name'),
            $request->validated('phone'),
            $request->validated('password'),
        );

        return ApiResponse::success([], 'OTP sent successfully.');
    }

    public function verifyOtp(AppUserVerifyOtpRequest $request): JsonResponse
    {
        $result = $this->appUserAuthService->verifyOtpAndIssueToken(
            $request->validated('phone'),
            $request->validated('otp'),
        );

        return ApiResponse::success(
            AppUserAuthResource::make($result)->toArray($request),
            'Authenticated successfully.',
        );
    }
}

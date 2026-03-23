<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\AppUserLoginRequest;
use App\Http\Requests\Auth\AppUserRegisterRequest;
use App\Http\Requests\Auth\AppUserVerifyOtpRequest;
use App\Http\Resources\Auth\AppUserAuthResource;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AppUserAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppUserAuthController
{
    public function __construct(protected AppUserAuthService $appUserAuthService) {}

    public function register(AppUserRegisterRequest $request): JsonResponse
    {
        $this->appUserAuthService->register(
            $request->validated('name'),
            $request->validated('phone'),
            $request->validated('password'),
        );

        return ApiResponse::success([], __('api.register_success'));
    }

    public function verifyOtp(AppUserVerifyOtpRequest $request): JsonResponse
    {
        $result = $this->appUserAuthService->verifyRegistrationOtp(
            $request->validated('phone'),
            $request->validated('otp'),
        );

        return ApiResponse::success(
            AppUserAuthResource::make($result)->toArray($request),
            __('api.authenticated_success'),
        );
    }

    public function login(AppUserLoginRequest $request): JsonResponse
    {
        $result = $this->appUserAuthService->login(
            $request->validated('phone'),
            $request->validated('password'),
        );

        return ApiResponse::success(
            AppUserAuthResource::make($result)->toArray($request),
            __('api.authenticated_success'),
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->appUserAuthService->logout($request->user());

        return ApiResponse::success([], __('api.logout_success'));
    }
}

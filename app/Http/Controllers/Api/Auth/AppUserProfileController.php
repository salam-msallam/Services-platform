<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\UpdateAppUserPasswordRequest;
use App\Http\Requests\Auth\UpdateAppUserProfileRequest;
use App\Http\Requests\Auth\VerifyAppUserPhoneUpdateRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Auth\AppUserAuthService;
use Illuminate\Http\JsonResponse;

    class AppUserProfileController
{
    public function __construct(protected AppUserAuthService $appUserAuthService) {}

    public function show(UpdateAppUserProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success(
            $this->appUserAuthService->profile($user),
            __('api.profile_fetched'),
        );
    }

    public function update(UpdateAppUserProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $result = $this->appUserAuthService->updateProfile($user, $request->validated());
        $message = $result['phone_verification_required']
            ? __('api.phone_update_otp_sent')
            : __('api.profile_updated');

        return ApiResponse::success($result, $message);
    }

    public function updatePassword(UpdateAppUserPasswordRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success(
            $this->appUserAuthService->updatePassword($user, $request->validated('password')),
            __('api.password_updated'),
        );
    }

    public function verifyPhone(VerifyAppUserPhoneUpdateRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success(
            $this->appUserAuthService->verifyPhoneUpdate(
                $user,
                $request->validated('phone'),
                $request->validated('otp'),
            ),
            __('api.phone_updated'),
        );
    }
}

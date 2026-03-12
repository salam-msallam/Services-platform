<?php

namespace App\Services\Auth;

use App\Models\AppUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AppUserAuthService
{

    public function __construct(protected OtpService $otpService) {}

    public function requestOtp(string $name, string $phone, string $password): void
    {
        $appUser = DB::transaction(function () use ($name, $phone, $password): AppUser {
            $appUser = AppUser::query()->where('phone', $phone)->first();

            if ($appUser) {
                $appUser->user->update(['name' => $name]);

                return $appUser;
            }

            $user = User::query()->create([
                'name' => $name,
                'password' => $password,
                'type' => 'app_user',
            ]);

            return $user->appUser()->create([
                'phone' => $phone,
            ]);
        });

        $this->otpService->generateAndSendOtpForAppUser($appUser);
    }

    /**
     * @return array{user: User, token: string, token_type: string, expires_in: int}
     */
    public function verifyOtpAndIssueToken(string $phone, string $code): array
    {
        $otpRecord = $this->otpService->verifyOtp($phone, $code);

        $user = User::query()->with('appUser')->findOrFail($otpRecord->user_id);

        $result = $user->createToken('app_user_token');

        return [
            'user' => $user,
            'token' => $result->accessToken,
        ];
    }
}

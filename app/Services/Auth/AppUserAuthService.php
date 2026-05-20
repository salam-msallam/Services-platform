<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Exceptions\Auth\AppUserNotFoundException;
use App\Exceptions\Auth\InvalidAppUserCredentialsException;
use App\Exceptions\Auth\PhoneAlreadyRegisteredException;
use App\Exceptions\Auth\PhoneAlreadyVerifiedException;
use App\Exceptions\Auth\PhoneNotVerifiedException;
use App\Models\AppUser;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AppUserAuthService
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * Register a new app user and send OTP (only step that sends OTP).
     *
     * @throws PhoneAlreadyRegisteredException
     */
    public function register(string $name, string $phone, string $password): void
    {
        if (AppUser::query()->where('phone', $phone)->exists()) {
            throw new PhoneAlreadyRegisteredException(__('api.phone_already_registered'));
        }

        $appUser = DB::transaction(function () use ($name, $phone, $password): AppUser {
            $user = User::query()->create([
                'name' => $name,
                'password' => $password,
                'type' => 'app_user',
            ]);

            $appUser = $user->appUser()->create([
                'phone' => $phone,
                'phone_verified_at' => null,
            ]);

            $role = Role::query()
                ->where('name', 'user')
                ->where('guard_name', 'api')
                ->first();

            if ($role !== null) {
                $user->syncRoles([$role]);
            }

            return $appUser;
        });

        $this->otpService->generateAndSendOtpForAppUser($appUser->fresh());
    }

    public function verifyRegistrationOtp(string $phone, string $code)
    {
        $appUser = AppUser::query()
            ->where('phone', $phone)
            ->with('user')
            ->first();

        if ($appUser === null) {
            throw new AppUserNotFoundException(__('api.app_user_not_found'));
        }

        if ($appUser->phone_verified_at !== null) {
            throw new PhoneAlreadyVerifiedException(__('api.phone_already_verified'));
        }

        $this->otpService->verifyOtp($phone, $code);

        DB::transaction(function () use ($appUser): void {
            $appUser->update(['phone_verified_at' => now()]);
        });

    }

    public function login(string $phone, string $password): array
    {
        $appUser = AppUser::query()
            ->where('phone', $phone)
            ->with('user')
            ->first();

        if ($appUser === null || $appUser->user === null) {
            throw new InvalidAppUserCredentialsException(__('api.invalid_credentials'));
        }

        $user = $appUser->user;

        if ($user->type !== 'app_user') {
            throw new InvalidAppUserCredentialsException(__('api.invalid_credentials'));
        }

        if ($appUser->phone_verified_at === null) {
            throw new PhoneNotVerifiedException(__('api.phone_not_verified'));
        }

        if (! Hash::check($password, $user->password)) {
            throw new InvalidAppUserCredentialsException(__('api.invalid_credentials'));
        }

        return $this->issueTokenPayload($user->load('appUser'));
    }

    /**
     * Revoke the current Passport access token for the authenticated user.
     */
    public function logout(?Authenticatable $user): void
    {
        if (!($user instanceof User)) {
            return;
        }

        if (!method_exists($user, 'token')) {
            return;
        }

        /** @var \Laravel\Passport\PersonalAccessToken|null $accessToken */
        $accessToken = $user->token();

        if ($accessToken !== null) {
            $accessToken->revoke();
        }
    }

    /**
     * @return array{name: string|null, phone: string|null, phone_verified_at: string|null}
     */
    public function profile(User $user): array
    {
        $user->loadMissing('appUser');

        return $this->profilePayload($user);
    }

    /**
     * @param  array{name?: string, phone?: string}  $data
     * @return array{profile: array{name: string|null, phone: string|null, phone_verified_at: string|null}, phone_verification_required: bool, pending_phone: string|null}
     */
    public function updateProfile(User $user, array $data): array
    {
        $user->loadMissing('appUser');

        DB::transaction(function () use ($user, $data): void {
            if (array_key_exists('name', $data)) {
                $user->update([
                    'name' => $data['name'],
                ]);
            }
        });

        $pendingPhone = null;
        $currentPhone = $user->appUser?->phone;

        if (array_key_exists('phone', $data) && $data['phone'] !== $currentPhone) {
            $pendingPhone = $data['phone'];
            $this->otpService->generateAndSendOtpForPhone((int) $user->id, $pendingPhone);
        }

        return [
            'profile' => $this->profilePayload($user->refresh()->load('appUser')),
            'phone_verification_required' => $pendingPhone !== null,
            'pending_phone' => $pendingPhone,
        ];
    }

    /**
     * @return array{name: string|null, phone: string|null, phone_verified_at: string|null}
     */
    public function updatePassword(User $user, string $password): array
    {
        $user->update([
            'password' => $password,
        ]);

        return $this->profilePayload($user->refresh()->load('appUser'));
    }

    /**
     * @return array{name: string|null, phone: string|null, phone_verified_at: string|null}
     */
    public function verifyPhoneUpdate(User $user, string $phone, string $otp): array
    {
        $user->loadMissing('appUser');

        $this->otpService->verifyOtpForUser((int) $user->id, $phone, $otp);

        DB::transaction(function () use ($user, $phone): void {
            $user->appUser?->update([
                'phone' => $phone,
                'phone_verified_at' => now(),
            ]);
        });

        return $this->profilePayload($user->refresh()->load('appUser'));
    }

    private function issueTokenPayload(User $user): array
    {
        $tokenResult = $user->createToken('app_user_token');

        return [
            'user' => $user,
            'token' => $tokenResult->accessToken,
        ];
    }

    /**
     * @return array{name: string|null, phone: string|null, phone_verified_at: string|null}
     */
    private function profilePayload(User $user): array
    {
        return [
            'name' => $user->name,
            'phone' => $user->appUser?->phone,
            'phone_verified_at' => $user->appUser?->phone_verified_at?->toISOString(),
        ];
    }
}

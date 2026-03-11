<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\InvalidOtpException;
use App\Models\AppUser;
use App\Models\OtpVerification;
use App\Services\Messaging\UltraMsgWhatsAppService;
use Illuminate\Support\Facades\DB;

final class OtpService
{
    private const OTP_LENGTH = 6;

    private const OTP_EXPIRY_MINUTES = 10;

    public function __construct(protected UltraMsgWhatsAppService $ultraMsgWhatsAppService) {}

    public function generateAndSendOtpForAppUser(AppUser $appUser): void
    {
        $code = $this->generateCode();

        DB::transaction(function () use ($appUser, $code): void {
            OtpVerification::query()
                ->where('identifier', $appUser->phone)
                ->whereNull('verified_at')
                ->update(['verified_at' => now()]);

            OtpVerification::query()->create([
                'user_id' => $appUser->user_id,
                'identifier' => $appUser->phone,
                'otp_code' => $code,
                'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            ]);
        });

        $this->ultraMsgWhatsAppService->sendOtp(
            $appUser->phone,
            __('Your verification code is: :code', ['code' => $code])
        );
    }

    public function verifyOtp(string $phone, string $code): OtpVerification
    {
        $record = OtpVerification::query()
            ->where('identifier', $phone)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->first();

        if (! $record || ! hash_equals((string) $record->otp_code, (string) $code)) {
            throw new InvalidOtpException();
        }

        DB::transaction(function () use ($record): void {
            $record->update(['verified_at' => now()]);
        });

        return $record;
    }

    private function generateCode(): string
    {
        $min = (int) str_pad('1', self::OTP_LENGTH, '0');
        $max = (int) str_pad('9', self::OTP_LENGTH, '9');

        return (string) random_int($min, $max);
    }
}

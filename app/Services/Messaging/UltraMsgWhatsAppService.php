<?php

namespace App\Services\Messaging;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

final class UltraMsgWhatsAppService
{
    private const ENDPOINT_PATH = '/messages/chat';

    public function __construct(
        private readonly ?string $instanceId = null,
        private readonly ?string $token = null,
        private readonly ?string $baseUrl = null,
    ) {
    }

    private function instanceId(): string
    {
        return $this->instanceId ?? config('services.ultramsg.instance_id', '');
    }

    private function token(): string
    {
        return $this->token ?? config('services.ultramsg.token', '');
    }

    private function baseUrl(): string
    {
        return $this->baseUrl ?? config('services.ultramsg.base_url', 'https://api.ultramsg.com');
    }

    public function sendOtp(string $phone, string $message): void
    {
        $url = rtrim($this->baseUrl(), '/').'/'.$this->instanceId().self::ENDPOINT_PATH;

        $response = $this->http()
            ->asForm()
            ->post($url, [
                'token' => $this->token(),
                'to' => $this->normalizePhone($phone),
                'body' => $message,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'UltraMsg WhatsApp API error: '.$response->body(),
                $response->status()
            );
        }
    }

    private function http(): PendingRequest
    {
        return Http::timeout(15);
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        return str_starts_with($phone, '0') ? '+963'.substr($phone, 1) : '+'.$phone;
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

final class FirebaseMessagingSwController
{
    public function __invoke(): Response
    {
        $config = config('services.firebase_web', []);
        unset($config['vapidKey']);
        $config = array_filter(
            $config,
            static fn (mixed $value): bool => $value !== null && $value !== '',
        );

        if (($config['apiKey'] ?? '') === '') {
            return response(
                '// Firebase web client is not configured (missing FIREBASE_WEB_* in .env).',
                200,
                [
                    'Content-Type' => 'application/javascript; charset=UTF-8',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                ],
            );
        }

        $version = (string) config('services.firebase_sw_compat_version', '11.6.0');

        $content = View::make('fcm.firebase-messaging-sw', [
            'version' => $version,
            'firebaseConfig' => $config,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/javascript; charset=UTF-8',
            'Service-Worker-Allowed' => '/',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}

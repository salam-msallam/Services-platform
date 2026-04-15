<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ultramsg' => [
        'instance_id' => env('ULTRAMSG_INSTANCE_ID'),
        'token' => env('ULTRAMSG_TOKEN'),
        'from' => env('ULTRAMSG_FROM'),
        'base_url' => env('ULTRAMSG_BASE_URL', 'https://api.ultramsg.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase (FCM)
    |--------------------------------------------------------------------------
    |
    | Used by kreait/laravel-firebase. Prefer FIREBASE_CREDENTIALS pointing to
    | your service account JSON (see .env.example).
    |
    */
    'firebase' => [
        'credentials' => env('FIREBASE_CREDENTIALS'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Web (FCM in browser / admin dashboard)
    |--------------------------------------------------------------------------
    |
    | Public config from Firebase Console → Project settings → Your apps (Web).
    | VAPID key: Cloud Messaging → Web configuration → Web Push certificates.
    | The compat CDN version should stay in sync with firebase_sw_compat_version.
    |
    */
    'firebase_sw_compat_version' => env('FIREBASE_SW_COMPAT_VERSION', '11.6.0'),

    'firebase_web' => [
        'apiKey' => env('FIREBASE_WEB_API_KEY'),
        'authDomain' => env('FIREBASE_WEB_AUTH_DOMAIN'),
        'projectId' => env('FIREBASE_WEB_PROJECT_ID'),
        'storageBucket' => env('FIREBASE_WEB_STORAGE_BUCKET'),
        'messagingSenderId' => env('FIREBASE_WEB_MESSAGING_SENDER_ID'),
        'appId' => env('FIREBASE_WEB_APP_ID'),
        'vapidKey' => env('FIREBASE_WEB_VAPID_KEY'),
    ],

];

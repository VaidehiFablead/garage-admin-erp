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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
    ],

    'removebg' => [
        'key' => env('REMOVE_BG_API_KEY'),
    ],

    'cleartax' => [
        'key' => env('GST_API_KEY') ?: env('CLEARTAX_AUTH_TOKEN'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    /*
    | Local Baileys bridge used by Personal WhatsApp (QR / WhatsApp Web style).
    | Start with: cd whatsapp-session-server && npm install && npm start
    */
    'whatsapp_session' => [
        'url' => env('WHATSAPP_SESSION_URL', env('WHATSAPP_BRIDGE_URL', 'http://127.0.0.1:3001')),
        'secret' => env('WHATSAPP_SESSION_SECRET', env('WHATSAPP_BRIDGE_SECRET', 'wa_session_secret_key_2026')),
        'timeout' => (int) env('WHATSAPP_SESSION_TIMEOUT', 15),
    ],

];

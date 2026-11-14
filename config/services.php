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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
    ],

    'beritapraktis' => [
        'admin_email' => env('ADMIN_EMAIL', 'admin@beritapraktis.com'),
        'outbound_link_rel' => env('ARTICLE_OUTBOUND_LINK_REL', 'noopener'),
        'min_word_count' => 900,
        'max_outbound_links' => 2,
        'max_articles_per_month' => (int) env('MAX_ARTICLES_PER_MONTH', 20),
        'public_upload_path' => env('PUBLIC_UPLOAD_PATH'),
    ],

    'pixabay' => [
        'key' => env('PIXABAY_API_KEY'),
    ],

    'pexels' => [
        'key' => env('PEXELS_API_KEY'),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    ],

];

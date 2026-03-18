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

    'sms' => [
        'simulate' => env('SMS_SIMULATE', true),
        'verify_ssl' => env('SMS_VERIFY_SSL', true),
        'ca_bundle' => env('SMS_CA_BUNDLE'),
    ],

    'infobip' => [
        'base_url' => env('INFOBIP_BASE_URL'),
        'api_key' => env('INFOBIP_API_KEY'),
        'from' => env('INFOBIP_FROM', 'COPPEC EBEN'),
        'simulate' => env('INFOBIP_SIMULATE', false),
    ],

    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_FROM'),
        'simulate' => env('TWILIO_SIMULATE', false),
    ],

    'zitasms' => [
        'base_url' => env('ZITASMS_BASE_URL', 'https://my.zitasms.com'),
        'api_key' => env('ZITASMS_API_KEY'),
        'device' => env('ZITASMS_DEVICE', 0),
        'simulate' => env('ZITASMS_SIMULATE', false),
    ],

];

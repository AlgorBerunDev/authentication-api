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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'telegram' => [
        'token' => env('TELEGRAM', ''),
    ],

    'playMobile' => [
        'login' => env('PLAY_MOBILE_LOGIN', ''),
        'password' => env('PLAY_MOBILE_PASSWORD', ''),
    ],

    // 'stripe' => [
    //     'model' => App\Domain\Users\Models\User::class,
    //     'key' => env('STRIPE_KEY'),
    //     'secret' => env('STRIPE_SECRET'),
    //     'webhook' => [
    //         'secret' => env('STRIPE_WEBHOOK_SECRET'),
    //         'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    //     ],
    // ],

    'confirmation' => [
        'send_limit' => intval(env('CONFIRMATION_SEND_LIMIT', 5)), // the number of sending the maximum verification code in the set time interval
        'send_time' => env('CONFIRMATION_SEND_TIME', "PT10M"), // the confirmation code sending counter counts during this time interval
        'send_block' => env('CONFIRMATION_SEND_BLOCK',"PT10M"), // block sending the confirmation code to the client for 10 minutes
        'max_attempts' => intval(env('CONFIRMATION_MAX_ATTEMPTS', 20)), // the number of attempts to verify the verification code to the maximum
        'validity_period' => env('CONFIRMATION_VALIDITY_PERIOD', 'PT2M'), // validity period of the confirmation code

        'user_send_limit' => intval(env('CONFIRMATION_USER_SEND_LIMIT', 20)),
        'user_send_time' => env('CONFIRMATION_USER_SEND_TIME', "PT20M"),
        'user_send_block' => env('CONFIRMATION_USER_SEND_BLOCK', "PT20M"),
    ],

    'login_attempt' => [
        'limit' => intval(env('LOGIN_ATTEMPT_LIMIT', 5)),
        'period' => env('LOGIN_ATTEMPT_PERIOD', "PT10M"),
        'block_duration' => env('LOGIN_ATTEMPT_BLOCK_DURATION', "PT10M")
    ],
];

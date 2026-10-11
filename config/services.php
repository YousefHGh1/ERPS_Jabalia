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

    'craft_licenses' => [
        // عند تفعيل هذا الخيار يتم ترصيد رسوم الحرف السنوية تلقائياً عبر جدولة الكرون
        // الإعداد الافتراضي false ليتوافق مع سياسة احتساب الرسوم عند التجديد الفعلي فقط.
        'auto_accrue' => env('CRAFT_LICENSES_AUTO_ACCRUE', false),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Citizen API (Government SSO)
    |--------------------------------------------------------------------------
    | Credentials for the government citizen data service (ws.gov.ps).
    | Set these in .env; never commit real values to version control.
    */
    'citizen_api' => [
        'client_id' => env('CITIZEN_API_CLIENT_ID', ''),
        'client_secret' => env('CITIZEN_API_CLIENT_SECRET', ''),
        'api_key' => env('CITIZEN_API_KEY'),
    ],

];

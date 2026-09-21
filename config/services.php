<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'registro_academico' => [
        // Dirección del WSDL del servicio (…/consultaEstudianteRyEv2.0.php?wsdl).
        'url' => env('REGISTRO_ACADEMICO_URL'),
        'dependencia' => env('REGISTRO_ACADEMICO_DEPENDENCIA', 'epsum'),
        'login' => env('REGISTRO_ACADEMICO_LOGIN', 'epsumWS'),
        'password' => env('REGISTRO_ACADEMICO_PASSWORD'),
        'timeout' => (int) env('REGISTRO_ACADEMICO_TIMEOUT', 10),
        // Solo para la prueba en vivo (tests/Feature/Estudiante/RegistroAcademicoEnVivoTest.php).
        'prueba_carnet' => env('REGISTRO_ACADEMICO_PRUEBA_CARNET'),
        'prueba_dpi' => env('REGISTRO_ACADEMICO_PRUEBA_DPI'),
    ],

    'google_maps' => [
        'key' => env('GOOGLE_MAPS_API_KEY'),
        'map_id' => env('GOOGLE_MAPS_MAP_ID', 'DEMO_MAP_ID'),
    ],

];

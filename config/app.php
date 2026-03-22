<?php

return [
    'name' => env('APP_NAME', 'SistemaColegios'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'domain' => env('APP_DOMAIN', 'localhost'),
    'timezone' => 'America/Lima',
    'locale' => 'es',
    'fallback_locale' => 'en',
    'faker_locale' => 'es_PE',
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'maintenance' => [
        'driver' => 'file',
    ],
];

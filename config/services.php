<?php

return [
    'mercadopago' => [
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN', ''),
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY', ''),
    ],

    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN', ''),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', ''),
    ],
];

<?php

return [
    'name' => 'LaravelPWA',
    'manifest' => [
        'name' => env('APP_NAME', 'Splitify'),
        'short_name' => 'Splitify',
        'start_url' => '/',
        'background_color' => '#000000',
        'theme_color' => '#111827',
        'display' => 'standalone',
        'orientation' => 'any',
        'status_bar' => 'black',
        'icons' => [
            '192x192' => [
                'path' => '/favicon-192x192.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/favion-384x384.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/favicon-512x512.png',
                'purpose' => 'any'
            ],
        ],
    ]
];

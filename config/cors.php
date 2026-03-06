<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [

        'https://fahrradhauskauf.com',
        'https://www.fahrradhauskauf.com',
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'https://*.ngrok-free.app', 
    ],
    'allowed_origins_patterns' => [
        '/https:\/\/.*\.ngrok-free\.app$/',  
    ],
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
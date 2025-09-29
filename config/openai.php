<?php

return [
    'api_key'      => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),
    'default'      => env('OPENAI_MODEL', 'gpt-5.1-mini'),
    'project'      => env('OPENAI_PROJECT'),

    'headers' => [
        // 'OpenAI-Beta' => 'assistants=v2',
    ],

    // خيارات HTTP فقط (بدون إنشاء Client هنا)
    'http' => [
        // على ويندوز: إن ما عندك شهادة، مؤقتًا استعمل false للتجارب
        'verify'          => env('HTTP_VERIFY', false),
        // أو لو عندك ملف شهادة:
        // 'verify'       => storage_path('certs/cacert.pem'),

        'timeout'         => env('HTTP_TIMEOUT', 30),
        'connect_timeout' => env('HTTP_CONNECT_TIMEOUT', 5),
        'read_timeout'    => env('HTTP_READ_TIMEOUT', 25),
        'http_errors'     => false,
        'base_uri'        => 'https://api.openai.com/v1/',
    ],
];

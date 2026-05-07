<?php

return [
    'rate_limit' => [
        'max_attempts' => env('QUOTES_API_RATE_LIMIT',30),
        'window_in_seconds' => env('QUOTES_API_RATE_WINDOW',60) ,
    ],
    'api_base_url' => env('QUOTES_API_BASE_URL','https://dummyjson.com/quotes'),
    'cache_store' => env('QUOTES_CACHE_STORE', 'file'),
    'cache_ttl' => env('QUOTES_CACHE_TTL', 25200), //
    'per_page_default' => env('QUOTES_PER_PAGE', 10)
];
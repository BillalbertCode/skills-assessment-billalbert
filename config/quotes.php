<?php

return [
    'rate_limit' => [
        'max_attempts' => env('QUOTES_API_RATE_LIMIT',30),
        'window_in_seconds' => env('QUOTES_API_RATE_WINDOW',60) ,
    ],
];
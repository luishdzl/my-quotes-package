<?php
return [
    'base_url'   => env('QUOTES_API_BASE_URL', 'https://dummyjson.com'),
    'rate_limit' => env('QUOTES_API_RATE_LIMIT', 60),
    'time_window'=> env('QUOTES_API_TIME_WINDOW', 60),
];

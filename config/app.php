<?php


return [
    
    'env' => env('APP_ENV', 'local'),
    'key' => env('APP_KEY', 'temporary key'), //used for encryption purposes
    'storage_path' => __DIR__ . "/../storage",
    'base_url' => env("APP_BASE_URL", "http://localhost"),
];

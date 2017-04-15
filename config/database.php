<?php

return [
    // Ref: http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
    'dbal' => [
        'default' => env('DATABASE_CONNECTION'),
        'testing' => 'sqlite_testing',
        
        'sqlite' => [
            'path' => env('DATABASE_SQLITE_PATH', config('app.storage_path') . "/db.sqlite"),
            'memory' => env('DATABASE_SQLITE_IN_MEMORY', false),
            'user' => "",
            'password' => "",
            'driver' => 'pdo_sqlite',
        ],
        
        'sqlite_testing' => [
            'memory' => true,
            'user' => "",
            'password' => "",
            'driver' => 'pdo_sqlite',
        ],
    ],
];


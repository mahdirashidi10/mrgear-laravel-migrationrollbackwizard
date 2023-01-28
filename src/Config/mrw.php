<?php
return [
    'migration' => [
        'directory' => database_path('migrations'),
        'database' => [
            'name' => 'migrations',
            'countable' => 'batch',
            'file' => 'migration'
        ],
        'exclude' => [

        ],
        'include' => [

        ]
    ],
    'seeder' => [
        'directory' => database_path('seeders'),
        'exclude' => [

        ],
        'include' => [

        ]
    ]
];
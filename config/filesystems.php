<?php

    return [

        // 'default' => env('FILESYSTEM_DRIVER', 'local'),
        'default' => env('FILESYSTEM_DRIVER', 'public'),

        'cloud' => env('FILESYSTEM_CLOUD', 's3'),

        'disks' => [

            'local' => [
                'driver' => 'local',
                'root' => storage_path('app'),
            ],

            'public' => [
                'driver' => 'local',
                // 'root' => storage_path('app/public'),
                'root' => public_path('storage'),
                'url' => env('APP_URL').'/storage',
                'visibility' => 'public',
            ],

            's3' => [
                'driver' => 's3',
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION'),
                'bucket' => env('AWS_BUCKET'),
                'url' => env('AWS_URL'),
            ],

            'google' => [
                'driver'        => 'google',
                'clientId'      => env('GOOGLE_DRIVE_CLIENT_ID'),
                'clientSecret'  => env('GOOGLE_DRIVE_CLIENT_SECRET'),
                'refreshToken'  => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
                'folderId'      => env('GOOGLE_DRIVE_FOLDER_ID'),
            ],

        ],

    ];

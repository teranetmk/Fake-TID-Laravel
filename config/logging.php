<?php

    use Monolog\Handler\StreamHandler;
    use Monolog\Handler\SyslogUdpHandler;

    return [

        'default' => env('LOG_CHANNEL', 'bugsnag'),

        'channels' => [
            'stack' => [
                'driver' => 'stack',
                'channels' => ['daily', 'bugsnag'],
            ],

            'single' => [
                'driver' => 'single',
                'path' => storage_path('logs/laravel.log'),
                'level' => 'debug',
            ],

            'daily' => [
                'driver' => 'daily',
                'path' => storage_path('logs/laravel.log'),
                'level' => 'debug',
                'days' => 14,
            ],

            'slack' => [
                'driver' => 'slack',
                'url' => env('LOG_SLACK_WEBHOOK_URL'),
                'username' => 'Laravel Log',
                'emoji' => ':boom:',
                'level' => 'critical',
            ],

            'papertrail' => [
                'driver'  => 'monolog',
                'level' => 'debug',
                'handler' => SyslogUdpHandler::class,
                'handler_with' => [
                    'host' => env('PAPERTRAIL_URL'),
                    'port' => env('PAPERTRAIL_PORT'),
                ],
            ],

            'stderr' => [
                'driver' => 'monolog',
                'handler' => StreamHandler::class,
                'with' => [
                    'stream' => 'php://stderr',
                ],
            ],

            'syslog' => [
                'driver' => 'syslog',
                'level' => 'debug',
            ],

            'errorlog' => [
                'driver' => 'errorlog',
                'level' => 'debug',
            ],

            'bugsnag' => [
                'driver' => 'bugsnag',
            ],
        ],

    ];

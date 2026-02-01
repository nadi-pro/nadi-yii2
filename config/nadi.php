<?php

/**
 * Nadi monitoring configuration for Yii 2.
 *
 * Add to your application config:
 *
 * 'components' => [
 *     'nadi' => require __DIR__ . '/nadi.php',
 * ],
 * 'bootstrap' => ['nadi'],
 */

return [
    'class' => \Nadi\Yii2\NadiComponent::class,
    'enabled' => true,
    'driver' => 'log', // log, http, opentelemetry
    'connections' => [
        'log' => [
            'path' => '@runtime/nadi',
        ],
        'http' => [
            'api_key' => getenv('NADI_API_KEY') ?: '',
            'app_key' => getenv('NADI_APP_KEY') ?: '',
            'endpoint' => 'https://nadi.pro/api',
            'version' => 'v1',
        ],
        'opentelemetry' => [
            'endpoint' => 'http://localhost:4318',
            'service_name' => getenv('APP_NAME') ?: 'yii2-app',
            'service_version' => '1.0.0',
            'environment' => defined('YII_ENV') ? YII_ENV : 'production',
        ],
    ],
    'query' => [
        'slow_threshold' => 500, // milliseconds
    ],
    'http' => [
        'hidden_request_headers' => [
            'Authorization',
            'php-auth-pw',
        ],
        'hidden_parameters' => [
            'password',
            'password_confirmation',
        ],
        'ignored_status_codes' => [
            '200-307',
        ],
    ],
    'sampling' => [
        'strategy' => 'fixed_rate', // fixed_rate, dynamic_rate, interval, peak_load
        'config' => [
            'sampling_rate' => 0.1,
            'base_rate' => 0.05,
            'load_factor' => 1.0,
            'interval_seconds' => 60,
        ],
    ],
];

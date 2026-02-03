# Quick Start

This guide walks through a minimal setup to get Nadi monitoring running in a Yii 2 application.

## Minimal Configuration

Add the following to `config/web.php`:

```php
return [
    'bootstrap' => ['nadi'],
    'components' => [
        'nadi' => [
            'class' => \Nadi\Yii2\NadiComponent::class,
            'enabled' => true,
            'driver' => 'log',
            'connections' => [
                'log' => [
                    'path' => '@runtime/nadi',
                ],
            ],
        ],
    ],
];
```

This uses the `log` driver, which writes monitoring data to files in the `@runtime/nadi` directory.
No external services are required.

## What Gets Monitored

With the default bootstrap configuration, Nadi automatically captures:

- **Exceptions** - Unhandled exceptions with full stack traces and code context
- **HTTP requests** - Request/response details for non-2xx/3xx status codes
- **Slow queries** - SQL queries exceeding the slow threshold (default: 500ms)

## Using the HTTP Driver

To send data to the Nadi platform, switch to the HTTP driver:

```php
'nadi' => [
    'class' => \Nadi\Yii2\NadiComponent::class,
    'enabled' => true,
    'driver' => 'http',
    'connections' => [
        'http' => [
            'api_key' => getenv('NADI_API_KEY') ?: '',
            'app_key' => getenv('NADI_APP_KEY') ?: '',
            'endpoint' => 'https://nadi.pro/api',
            'version' => 'v1',
        ],
    ],
],
```

## Full Configuration Example

For a complete setup with HTTP monitoring, query monitoring, and sampling:

```php
return [
    'bootstrap' => ['nadi'],
    'components' => [
        'nadi' => [
            'class' => \Nadi\Yii2\NadiComponent::class,
            'enabled' => true,
            'driver' => 'http',
            'connections' => [
                'http' => [
                    'api_key' => getenv('NADI_API_KEY') ?: '',
                    'app_key' => getenv('NADI_APP_KEY') ?: '',
                    'endpoint' => 'https://nadi.pro/api',
                    'version' => 'v1',
                ],
            ],
            'query' => [
                'slow_threshold' => 500,
            ],
            'http' => [
                'hidden_request_headers' => ['Authorization', 'php-auth-pw'],
                'hidden_parameters' => ['password', 'password_confirmation'],
                'ignored_status_codes' => ['200-307'],
            ],
            'sampling' => [
                'strategy' => 'fixed_rate',
                'config' => [
                    'sampling_rate' => 0.1,
                ],
            ],
        ],
    ],
];
```

## Verify the Setup

Test that everything is working:

```bash
yii nadi/verify
```

## Next Steps

- [Console Commands](03-console-commands.md) - CLI tools for management
- [Configuration Reference](../03-configuration/README.md) - All available options

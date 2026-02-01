# Nadi for Yii 2

[![run-tests](https://github.com/nadi-pro/nadi-yii2/actions/workflows/run-tests.yml/badge.svg)](https://github.com/nadi-pro/nadi-yii2/actions/workflows/run-tests.yml)

Nadi monitoring SDK for Yii 2 applications. Monitor exceptions, slow queries, HTTP errors, and application performance in your Yii 2 projects.

## Requirements

- PHP 8.1+
- Yii 2.0.45+

## Installation

```bash
composer require nadi-pro/nadi-yii2
```

## Configuration

### 1. Add the component

In your application config (`config/web.php` or `config/main.php`):

```php
return [
    'bootstrap' => ['nadi'],
    'components' => [
        'nadi' => [
            'class' => \Nadi\Yii2\NadiComponent::class,
            'enabled' => true,
            'driver' => 'http', // log, http, opentelemetry
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
                    'service_name' => 'my-app',
                    'service_version' => '1.0.0',
                    'environment' => YII_ENV,
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

### 2. Register the bootstrap component

Ensure `'nadi'` is listed in the `bootstrap` array of your application config.

### 3. (Optional) Add behavior for OpenTelemetry

```php
return [
    'as otel' => [
        'class' => \Nadi\Yii2\Behavior\OpenTelemetryBehavior::class,
    ],
];
```

### 4. Console commands

In your console application config:

```php
'controllerMap' => [
    'nadi' => \Nadi\Yii2\Controllers\NadiController::class,
],
```

### 5. Environment variables

```
NADI_API_KEY=your-api-key
NADI_APP_KEY=your-app-key
```

## Features

- **Exception Monitoring**: Automatically captures unhandled exceptions
- **HTTP Monitoring**: Tracks HTTP requests and error responses via `EVENT_AFTER_REQUEST`
- **Database Monitoring**: Monitors slow SQL queries via Yii 2 DB events
- **OpenTelemetry**: Trace context propagation via application behavior
- **User Context**: Automatically captures authenticated user via `Yii::$app->user->identity`

## Console Commands

```bash
# Install configuration and shipper
yii nadi/install

# Test the connection
yii nadi/test

# Verify configuration
yii nadi/verify

# Update shipper binary
yii nadi/update-shipper
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

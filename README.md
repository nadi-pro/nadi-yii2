# Nadi for Yii 2

[![run-tests](https://github.com/nadi-pro/nadi-yii2/actions/workflows/run-tests.yml/badge.svg)](https://github.com/nadi-pro/nadi-yii2/actions/workflows/run-tests.yml)

Nadi monitoring SDK for Yii 2 applications. Monitor exceptions, slow queries,
HTTP errors, and application performance in your Yii 2 projects.

## Requirements

- PHP 8.1+
- Yii 2.0.45+

## Installation

```bash
composer require nadi-pro/nadi-yii2
```

## Quick Start

Add to your application config (`config/web.php`):

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

## Features

- **Exception Monitoring** - Automatically captures unhandled exceptions
- **HTTP Monitoring** - Tracks HTTP requests and error responses
- **Database Monitoring** - Monitors slow SQL queries
- **OpenTelemetry** - Trace context propagation and OTLP export
- **User Context** - Captures authenticated user information
- **Sampling** - Fixed rate, dynamic rate, interval, and peak load strategies

## Documentation

See the [full documentation](docs/README.md) for detailed guides on:

- [Getting Started](docs/01-getting-started/README.md) - Installation and setup
- [Architecture](docs/02-architecture/README.md) - System design and components
- [Configuration](docs/03-configuration/README.md) - Drivers, monitoring, and sampling
- [Advanced](docs/04-advanced/README.md) - OpenTelemetry and testing

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

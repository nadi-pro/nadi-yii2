# Installation

This guide covers requirements and installation of the Nadi monitoring SDK for Yii 2.

## Requirements

- PHP 8.1 or higher
- Yii 2.0.45 or higher

## Install via Composer

```bash
composer require nadi-pro/nadi-yii2
```

## Register the Component

Add the Nadi component to your application configuration (`config/web.php` or `config/main.php`):

```php
return [
    'bootstrap' => ['nadi'],
    'components' => [
        'nadi' => [
            'class' => \Nadi\Yii2\NadiComponent::class,
            'enabled' => true,
            'driver' => 'log',
        ],
    ],
];
```

The `bootstrap` array entry ensures Nadi registers its event handlers when the application starts.

## Environment Variables

If using the HTTP driver, set your API credentials:

```text
NADI_API_KEY=your-api-key
NADI_APP_KEY=your-app-key
```

## Using the Config File

You can also use the bundled configuration file directly:

```php
return [
    'bootstrap' => ['nadi'],
    'components' => [
        'nadi' => require __DIR__ . '/nadi.php',
    ],
];
```

Publish the config file to your application using the install command:

```bash
yii nadi/install
```

## Next Steps

- [Quick Start](02-quick-start.md) - Minimal setup to start monitoring
- [Transport Drivers](../03-configuration/01-transport-drivers.md) - Configure HTTP or OpenTelemetry drivers

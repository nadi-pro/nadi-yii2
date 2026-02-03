# Console Commands

Nadi provides CLI commands for installation, connection testing, and shipper management.

## Setup

Register the controller in your console application config (`config/console.php`):

```php
'controllerMap' => [
    'nadi' => \Nadi\Yii2\Controllers\NadiController::class,
],
```

## Available Commands

### Install

```bash
yii nadi/install
```

Publishes the default `config/nadi.php` configuration file to your application and installs
the Nadi shipper binary. If the configuration file already exists, it is not overwritten.

### Test Connection

```bash
yii nadi/test
```

Tests the connection to the configured transport driver. Verifies that the Nadi component
is configured and the transporter can communicate with the remote endpoint.

### Verify Configuration

```bash
yii nadi/verify
```

Displays the current configuration status (enabled/disabled, active driver) and verifies
the transporter is properly initialized.

### Update Shipper

```bash
yii nadi/update-shipper
```

Downloads or updates the Nadi shipper binary. The binary is platform-specific and supports
Linux (amd64, 386, arm64), macOS (amd64, arm64), and Windows (amd64).

## Next Steps

- [Architecture Overview](../02-architecture/01-overview.md) - How Nadi works internally
- [Transport Drivers](../03-configuration/01-transport-drivers.md) - Driver configuration reference

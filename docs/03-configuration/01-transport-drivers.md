# Transport Drivers

Nadi supports three transport drivers for delivering monitoring data. Set the active driver
with the `driver` property and configure connection settings in the `connections` array.

## Log Driver

Writes monitoring data to local files. No external services required.

```php
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
```

| Option | Type   | Default         | Description                                    |
| ------ | ------ | --------------- | ---------------------------------------------- |
| `path` | string | `@runtime/nadi` | Directory for log files. Supports Yii aliases. |

## HTTP Driver

Sends data to the Nadi platform API. Requires API credentials.

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

| Option     | Type   | Default                | Description                            |
| ---------- | ------ | ---------------------- | -------------------------------------- |
| `api_key`  | string | `''`                   | API key from the Nadi platform         |
| `app_key`  | string | `''`                   | Application key from the Nadi platform |
| `endpoint` | string | `https://nadi.pro/api` | API endpoint URL                       |
| `version`  | string | `v1`                   | API version                            |

Set credentials via environment variables:

```text
NADI_API_KEY=your-api-key
NADI_APP_KEY=your-app-key
```

## OpenTelemetry Driver

Exports data to any OpenTelemetry-compatible collector (Jaeger, Grafana Tempo, etc.)
using the OTLP protocol.

```php
'nadi' => [
    'class' => \Nadi\Yii2\NadiComponent::class,
    'enabled' => true,
    'driver' => 'opentelemetry',
    'connections' => [
        'opentelemetry' => [
            'endpoint' => 'http://localhost:4318',
            'service_name' => 'my-app',
            'service_version' => '1.0.0',
            'environment' => YII_ENV,
        ],
    ],
],
```

| Option            | Type   | Default                 | Description                            |
| ----------------- | ------ | ----------------------- | -------------------------------------- |
| `endpoint`        | string | `http://localhost:4318` | OTLP HTTP endpoint                     |
| `service_name`    | string | `yii2-app`              | Service name reported to the collector |
| `service_version` | string | `1.0.0`                 | Service version                        |
| `environment`     | string | `YII_ENV`               | Deployment environment                 |

## Disabling Monitoring

Set `enabled` to `false` to disable all monitoring without removing configuration:

```php
'nadi' => [
    'class' => \Nadi\Yii2\NadiComponent::class,
    'enabled' => false,
],
```

## Next Steps

- [Monitoring Options](02-monitoring-options.md) - HTTP and query monitoring configuration
- [Sampling Strategies](03-sampling-strategies.md) - Control data volume with sampling

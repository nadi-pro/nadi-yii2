# OpenTelemetry

Nadi for Yii 2 supports OpenTelemetry for distributed tracing and telemetry export.
This includes OTLP transport, trace context propagation, and semantic convention attributes.

## OTLP Transport

Configure the OpenTelemetry driver to export data to any OTLP-compatible collector:

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

Compatible collectors include Jaeger, Grafana Tempo, and any OTLP HTTP receiver
on port 4318.

## Trace Context Propagation

The `OpenTelemetryBehavior` handles W3C Trace Context propagation by extracting and
injecting the `traceparent` header.

### Setup

Attach the behavior to your application:

```php
// config/web.php
return [
    'as otel' => [
        'class' => \Nadi\Yii2\Behavior\OpenTelemetryBehavior::class,
    ],
];
```

### How It Works

- **Before request** - Extracts the `traceparent` header from the incoming HTTP request
  and parses the trace ID, span ID, and trace flags
- **After request** - Injects the `traceparent` header into the response, allowing
  downstream services to continue the trace

The `traceparent` header follows the W3C format:

```text
00-{trace_id}-{span_id}-{trace_flags}
```

## Semantic Conventions

`src/Support/OpenTelemetrySemanticConventions.php` extends the core SDK conventions with
Yii 2-specific attributes.

### Yii-Specific Attributes

| Constant             | Value                | Description                    |
| -------------------- | -------------------- | ------------------------------ |
| `YII_CONTROLLER`     | `yii.controller`     | Active controller class        |
| `YII_ACTION`         | `yii.action`         | Active action ID               |
| `YII_MODULE`         | `yii.module`         | Active module ID               |
| `DB_CONNECTION_NAME` | `db.connection.name` | Database connection identifier |

### Attribute Methods

| Method                           | Context                                  |
| -------------------------------- | ---------------------------------------- |
| `httpAttributesFromYiiRequest()` | HTTP method, URL, status code, headers   |
| `httpAttributesFromGlobals()`    | Server globals fallback                  |
| `databaseAttributes()`           | Connection name, SQL statement, duration |
| `userAttributes()`               | Authenticated user ID and class          |
| `sessionAttributes()`            | Session ID                               |
| `exceptionAttributes()`          | Exception class, message, stack trace    |
| `performanceAttributes()`        | Memory usage, peak memory                |

## Local Development with Jaeger

Run a local Jaeger instance to test OpenTelemetry integration:

```bash
docker run -d --name jaeger \
  -p 4318:4318 \
  -p 16686:16686 \
  jaegertracing/all-in-one:latest
```

Access the Jaeger UI at `http://localhost:16686`.

Stop and remove when done:

```bash
docker stop jaeger && docker rm jaeger
```

## Next Steps

- [Testing](02-testing.md) - Running tests with OpenTelemetry
- [Transport Drivers](../03-configuration/01-transport-drivers.md) - Driver configuration reference

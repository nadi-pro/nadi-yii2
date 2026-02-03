# Components

This page describes the core classes that make up the Nadi for Yii 2 integration.

## NadiComponent

`src/NadiComponent.php` - Yii 2 application component that holds configuration and creates
the `Nadi` instance.

```php
class NadiComponent extends Component
{
    public bool $enabled = true;
    public string $driver = 'log';
    public array $connections = [];
    public array $query = ['slow_threshold' => 500];
    public array $http = [...];
    public array $sampling = [...];
}
```

Key behaviors:

- Extends `yii\base\Component` for standard Yii 2 component lifecycle
- Provides default connection settings for all drivers when `$connections` is empty
- Creates the `Nadi` singleton during `init()`
- Exposes `getNadi()` to access the monitoring instance
- Exposes `getConfigArray()` to pass configuration to handlers

## NadiBootstrap

`src/NadiBootstrap.php` - Implements `yii\base\BootstrapInterface` to register all monitoring
hooks during application startup.

Registers three types of monitoring:

- **Exception handler** - Wraps the existing exception handler to capture unhandled exceptions
  before passing them to the original handler
- **HTTP events** - Listens to `EVENT_AFTER_REQUEST` on web applications to capture request/response data
- **Query monitoring** - Listens to `yii\db\Command::EVENT_AFTER_EXECUTE` to capture slow queries

Also registers a shutdown handler via `EVENT_AFTER_REQUEST` to flush any buffered entries.

## Nadi

`src/Nadi.php` - Main singleton class that orchestrates the monitoring pipeline.

```php
class Nadi
{
    public static function getInstance(): ?self;
    public function isEnabled(): bool;
    public function store(Entry|ExceptionEntry $entry): void;
    public function recordException(\Throwable $exception): void;
    public function recordQuery(string $sql, float $duration, string $connectionName): void;
    public function send(): void;
}
```

Key behaviors:

- Singleton accessible via `getInstance()`, which also checks `Yii::$app->get('nadi')`
- `isEnabled()` returns `true` only when `enabled` is set and the transporter initialized
- `store()` passes entry data to the transporter service for sampling and delivery
- `send()` flushes buffered entries; also called automatically in `__destruct()`

## Transporter

`src/Transporter.php` - Configures the transport driver and sampling strategy based on the
provided configuration array.

Creates two things:

- A **transport driver** (`Nadi\Transporter\Log`, `Nadi\Transporter\Http`,
  or `Nadi\Transporter\OpenTelemetry`) configured with connection settings
- A **sampling manager** wrapping the selected sampling strategy with a `Nadi\Sampling\Config`
  object

These are combined into a `Nadi\Transporter\Service` instance exposed via `getService()`.

## Behaviors

### NadiBehavior

`src/Behavior/NadiBehavior.php` - Alternative to using `NadiBootstrap`. Attaches directly to the
application as a behavior and captures HTTP events on `EVENT_AFTER_REQUEST`.

```php
// config/web.php
'as nadi' => [
    'class' => \Nadi\Yii2\Behavior\NadiBehavior::class,
],
```

### OpenTelemetryBehavior

`src/Behavior/OpenTelemetryBehavior.php` - Handles W3C Trace Context propagation by extracting
the `traceparent` header from incoming requests and injecting it into responses.

```php
// config/web.php
'as otel' => [
    'class' => \Nadi\Yii2\Behavior\OpenTelemetryBehavior::class,
],
```

## Next Steps

- [Handlers](03-handlers.md) - How events are captured and processed
- [Configuration Reference](../03-configuration/README.md) - All configuration options

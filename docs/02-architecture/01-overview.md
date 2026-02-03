# Architecture Overview

This page describes the high-level architecture of Nadi for Yii 2 and how data flows through
the monitoring pipeline.

## Namespace Organization

```text
Nadi\Yii2\
├── Actions\          # Utility actions (exception context, property extraction)
├── Behavior\         # Yii 2 application behaviors
├── Concerns\         # Shared traits (metrics, stack traces)
├── Controllers\      # Console commands
├── Data\             # Entry models (extends core Nadi entries)
├── Handler\          # Event handlers (exception, HTTP, query)
├── Metric\           # Yii-specific metrics (HTTP, framework, application, network)
├── Shipper\          # Binary manager wrapper
├── Support\          # OpenTelemetry semantic conventions
├── Nadi.php          # Main singleton orchestrator
├── NadiBootstrap.php # Bootstrap event registration
├── NadiComponent.php # Yii 2 application component
└── Transporter.php   # Transport and sampling configuration
```

## Data Flow

The monitoring pipeline follows this sequence:

1. **Bootstrap** - `NadiBootstrap` registers event handlers when the application starts
2. **Capture** - Handlers capture exceptions, HTTP requests, or slow queries
3. **Entry** - Captured data is wrapped in `Entry` or `ExceptionEntry` objects with metadata
4. **Metrics** - Yii-specific metrics (framework version, route, user context) are attached
5. **Sampling** - The sampling manager determines whether the entry should be processed
6. **Transport** - The configured driver (log, HTTP, or OpenTelemetry) delivers the data
7. **Flush** - Remaining entries are sent on application shutdown via `EVENT_AFTER_REQUEST`

## Initialization Sequence

```text
Application Start
  └─ NadiBootstrap::bootstrap()
       ├─ NadiComponent::init()
       │    └─ new Nadi(config)
       │         └─ new Transporter(config)
       │              ├─ configureTransporter()  → Log / Http / OpenTelemetry
       │              └─ configureSampling()     → FixedRate / DynamicRate / etc.
       ├─ registerExceptionHandler()
       ├─ registerHttpEvents()
       ├─ registerQueryMonitoring()
       └─ EVENT_AFTER_REQUEST → send()
```

## Dependencies

Nadi for Yii 2 depends on:

| Package             | Purpose                                            |
| ------------------- | -------------------------------------------------- |
| `nadi-pro/nadi-php` | Core SDK: entries, transporters, sampling, metrics |
| `yiisoft/yii2`      | Yii 2 framework integration points                 |

The core SDK provides the transport layer (`Nadi\Transporter\*`), sampling strategies
(`Nadi\Sampling\*`), data structures (`Nadi\Data\*`), and metric collection (`Nadi\Metric\*`).
This package extends those with Yii 2-specific implementations.

## Next Steps

- [Components](02-components.md) - Core class details
- [Handlers](03-handlers.md) - Event handler internals

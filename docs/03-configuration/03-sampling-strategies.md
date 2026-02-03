# Sampling Strategies

Sampling controls the volume of data collected by determining whether each event should
be recorded. Configure the strategy and its parameters in the `sampling` section.

## Configuration

```php
'sampling' => [
    'strategy' => 'fixed_rate',
    'config' => [
        'sampling_rate' => 0.1,
    ],
],
```

## Available Strategies

### Fixed Rate

Samples events at a constant probability. The simplest strategy.

```php
'sampling' => [
    'strategy' => 'fixed_rate',
    'config' => [
        'sampling_rate' => 0.1, // 10% of events
    ],
],
```

| Parameter       | Type  | Default | Description                     |
| --------------- | ----- | ------- | ------------------------------- |
| `sampling_rate` | float | `0.1`   | Probability between 0.0 and 1.0 |

### Dynamic Rate

Adjusts sampling based on a base rate multiplied by a load factor.

```php
'sampling' => [
    'strategy' => 'dynamic_rate',
    'config' => [
        'base_rate' => 0.05,
        'load_factor' => 1.0,
    ],
],
```

| Parameter     | Type  | Default | Description                         |
| ------------- | ----- | ------- | ----------------------------------- |
| `base_rate`   | float | `0.05`  | Base sampling probability           |
| `load_factor` | float | `1.0`   | Multiplier applied to the base rate |

Effective rate = `base_rate * load_factor`.

### Interval

Samples events at regular time intervals. An event is sampled when
`current_timestamp % interval_seconds == 0`.

```php
'sampling' => [
    'strategy' => 'interval',
    'config' => [
        'interval_seconds' => 60,
    ],
],
```

| Parameter          | Type  | Default | Description                         |
| ------------------ | ----- | ------- | ----------------------------------- |
| `interval_seconds` | float | `60`    | Interval in seconds between samples |

### Peak Load

Adaptive sampling that considers system resource usage (CPU and memory). Events are
only sampled when system usage exceeds the effective rate, with additional randomness
applied.

```php
'sampling' => [
    'strategy' => 'peak_load',
    'config' => [
        'base_rate' => 0.05,
        'load_factor' => 1.0,
    ],
],
```

| Parameter     | Type  | Default | Description                         |
| ------------- | ----- | ------- | ----------------------------------- |
| `base_rate`   | float | `0.05`  | Base sampling probability           |
| `load_factor` | float | `1.0`   | Multiplier applied to the base rate |

System usage is calculated as the maximum of CPU load average (normalized by core count)
and memory usage percentage. Values are cached for 5 seconds.

## Strategy Comparison

| Strategy     | Best For                             | Predictability |
| ------------ | ------------------------------------ | -------------- |
| Fixed Rate   | General use, consistent sampling     | High           |
| Dynamic Rate | Adjustable load-based sampling       | Medium         |
| Interval     | Time-based periodic sampling         | High           |
| Peak Load    | High-traffic applications under load | Low            |

## Next Steps

- [Transport Drivers](01-transport-drivers.md) - Configure data delivery
- [Architecture Overview](../02-architecture/01-overview.md) - How sampling fits in the pipeline

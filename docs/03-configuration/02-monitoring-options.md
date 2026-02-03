# Monitoring Options

This page covers configuration for HTTP request monitoring, database query monitoring,
and data filtering options.

## HTTP Monitoring

HTTP monitoring captures request and response data on `EVENT_AFTER_REQUEST` for web
applications. Configure filtering to control what gets recorded.

```php
'http' => [
    'hidden_request_headers' => ['Authorization', 'php-auth-pw'],
    'hidden_parameters' => ['password', 'password_confirmation'],
    'ignored_status_codes' => ['200-307'],
],
```

### Hidden Request Headers

Headers listed here are replaced with `********` before storage. Comparison is
case-insensitive.

| Option                   | Type  | Default                            |
| ------------------------ | ----- | ---------------------------------- |
| `hidden_request_headers` | array | `['Authorization', 'php-auth-pw']` |

### Hidden Parameters

Body parameters listed here are replaced with `********`. Exact key match is required.

| Option              | Type  | Default                                 |
| ------------------- | ----- | --------------------------------------- |
| `hidden_parameters` | array | `['password', 'password_confirmation']` |

### Ignored Status Codes

Requests with matching status codes are not recorded. Supports individual codes and
ranges with a dash separator.

| Option                 | Type  | Default       |
| ---------------------- | ----- | ------------- |
| `ignored_status_codes` | array | `['200-307']` |

Examples:

```php
// Ignore all successful responses
'ignored_status_codes' => ['200-299'],

// Ignore specific codes
'ignored_status_codes' => ['200', '301', '302'],

// Combine ranges and individual codes
'ignored_status_codes' => ['200-307', '404'],
```

## Query Monitoring

Query monitoring captures slow SQL queries via the `yii\db\Command::EVENT_AFTER_EXECUTE`
event. Only queries exceeding the slow threshold are recorded.

```php
'query' => [
    'slow_threshold' => 500,
],
```

| Option           | Type | Default | Description                                        |
| ---------------- | ---- | ------- | -------------------------------------------------- |
| `slow_threshold` | int  | `500`   | Minimum duration in milliseconds to record a query |

Recorded query data includes:

- SQL statement
- Duration in milliseconds
- Connection name
- Caller file and line (extracted from stack trace, vendor paths excluded)

## Next Steps

- [Sampling Strategies](03-sampling-strategies.md) - Control data collection volume
- [Transport Drivers](01-transport-drivers.md) - Configure where data is sent

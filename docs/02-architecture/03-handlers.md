# Handlers

Handlers are responsible for capturing specific types of events, transforming them into
`Entry` objects, and passing them to the transporter for delivery.

## Base Handler

All handlers extend `Nadi\Yii2\Handler\Base`, which provides:

- Access to the `Nadi` instance via `$this->nadi`
- A `store()` method that passes data to the transporter service
- A `hash()` method for generating SHA-1 family hashes

## HandleExceptionEvent

`src/Handler/HandleExceptionEvent.php` - Captures unhandled exceptions.

Collects the following data:

| Field     | Description                                                      |
| --------- | ---------------------------------------------------------------- |
| `class`   | Exception class name                                             |
| `message` | Exception message                                                |
| `code`    | Exception code                                                   |
| `file`    | File where the exception occurred                                |
| `line`    | Line number                                                      |
| `trace`   | Formatted stack trace (file, line, function, class, type)        |
| `context` | Source code context around the exception (10 lines before/after) |

Generates a `hashFamily` from the exception class, file, line, message, and current date.
This groups related exceptions together in the monitoring dashboard.

## HandleHttpRequestEvent

`src/Handler/HandleHttpRequestEvent.php` - Captures HTTP request/response data.

Processing steps:

1. Checks if monitoring is enabled
2. Skips requests matching `ignored_status_codes` ranges
3. Creates an `Entry` of type `HTTP`
4. Filters sensitive headers and parameters based on configuration
5. Attaches OpenTelemetry semantic convention attributes

Captured fields include method, URI, status code, filtered headers, filtered body
parameters, and OTel HTTP attributes.

### Header Filtering

Headers listed in `hidden_request_headers` are replaced with `********`. Comparison
is case-insensitive.

### Parameter Filtering

Body parameters listed in `hidden_parameters` are replaced with `********`. Exact
key match is required.

### Status Code Filtering

The `ignored_status_codes` array supports both individual codes and ranges:

- `'200'` - Ignores exactly status 200
- `'200-307'` - Ignores all status codes from 200 to 307 inclusive

## HandleQueryEvent

`src/Handler/HandleQueryEvent.php` - Captures slow database queries.

Processing steps:

1. Checks if monitoring is enabled
2. Compares query duration against `slow_threshold` (default: 500ms)
3. Skips queries faster than the threshold
4. Extracts the caller file and line from the stack trace
5. Creates an `Entry` of type `QUERY` with OTel database attributes

The `FetchesStackTrace` trait filters out vendor paths to identify the application code
that initiated the query.

## Data Models

### Entry

`src/Data/Entry.php` - Extends the core `Nadi\Data\Entry` with Yii 2-specific features:

- Captures the authenticated user via `Yii::$app->user->identity`
- Registers Yii-specific metrics (HTTP, Framework, Application, Network)

### ExceptionEntry

`src/Data/ExceptionEntry.php` - Same extensions applied to exception entries.

## Next Steps

- [Monitoring Options](../03-configuration/02-monitoring-options.md) - Configure filtering and thresholds
- [Architecture Overview](01-overview.md) - High-level data flow

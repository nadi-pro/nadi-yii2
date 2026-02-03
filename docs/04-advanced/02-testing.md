# Testing

This page covers running the test suite and development workflow for Nadi for Yii 2.

## Running Tests

```bash
composer test
```

This runs PHPUnit with the configuration in `phpunit.xml`.

## Test Structure

```text
tests/
├── TestCase.php              # Base test class with helper methods
└── Feature/
    ├── BootstrapTest.php     # NadiBootstrap instantiation
    ├── ComponentTest.php     # NadiComponent configuration and singleton
    └── HandlerTest.php       # Exception and query handler instantiation
```

### Base Test Case

`tests/TestCase.php` provides a `getNadiConfig()` helper that returns a default
configuration array for testing:

```php
protected function getNadiConfig(): array
{
    return [
        'enabled' => true,
        'driver' => 'log',
        'connections' => [
            'log' => ['path' => '/tmp/nadi-test'],
        ],
        'query' => ['slow_threshold' => 500],
        'http' => [
            'hidden_request_headers' => ['Authorization'],
            'hidden_parameters' => ['password'],
            'ignored_status_codes' => ['200-307'],
        ],
        'sampling' => [
            'strategy' => 'fixed_rate',
            'config' => ['sampling_rate' => 0.1],
        ],
    ];
}
```

## Code Formatting

Format code with Laravel Pint:

```bash
composer format
```

## CI/CD

The GitHub Actions workflow (`.github/workflows/run-tests.yml`) runs on push and pull
requests to `main` and `2.x` branches. It tests against PHP 8.1, 8.2, 8.3, and 8.4.

The pipeline runs both the test suite and code formatting checks.

## Next Steps

- [Architecture Overview](../02-architecture/01-overview.md) - Understand the codebase structure
- [Getting Started](../01-getting-started/README.md) - Installation guide

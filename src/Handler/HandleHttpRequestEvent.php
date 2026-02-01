<?php

namespace Nadi\Yii2\Handler;

use Nadi\Data\Type;
use Nadi\Yii2\Data\Entry;
use Nadi\Yii2\Nadi;
use Nadi\Yii2\Support\OpenTelemetrySemanticConventions;
use yii\web\Request;
use yii\web\Response;

class HandleHttpRequestEvent extends Base
{
    private array $config;

    public function __construct(
        Nadi $nadi,
        array $config,
    ) {
        parent::__construct($nadi);
        $this->config = $config;
    }

    public function handle(Request $request, Response $response): void
    {
        if (! $this->nadi->isEnabled()) {
            return;
        }

        $statusCode = $response->getStatusCode();
        $httpConfig = $this->config['http'] ?? [];

        if ($this->isIgnoredStatusCode($statusCode, $httpConfig['ignored_status_codes'] ?? [])) {
            return;
        }

        $entry = new Entry(Type::HTTP);

        $entry->content = [
            'method' => $request->getMethod(),
            'uri' => $request->getAbsoluteUrl(),
            'status_code' => $statusCode,
            'headers' => $this->filterHeaders(
                $request->getHeaders()->toArray(),
                $httpConfig['hidden_request_headers'] ?? [],
            ),
            'payload' => $this->filterParameters(
                $request->getBodyParams() ?: [],
                $httpConfig['hidden_parameters'] ?? [],
            ),
            'response_status' => $statusCode,
        ];

        $entry->content = array_merge(
            $entry->content,
            OpenTelemetrySemanticConventions::httpAttributesFromYiiRequest($request, $response),
        );

        $this->store($entry->toArray());
    }

    protected function filterHeaders(array $headers, array $hidden): array
    {
        $hiddenLower = array_map('strtolower', $hidden);

        foreach ($headers as $key => $value) {
            if (in_array(strtolower($key), $hiddenLower, true)) {
                $headers[$key] = '********';
            }
        }

        return $headers;
    }

    protected function filterParameters(array $parameters, array $hidden): array
    {
        foreach ($hidden as $key) {
            if (isset($parameters[$key])) {
                $parameters[$key] = '********';
            }
        }

        return $parameters;
    }

    protected function isIgnoredStatusCode(int $statusCode, array $ignoredRanges): bool
    {
        foreach ($ignoredRanges as $range) {
            if (str_contains($range, '-')) {
                [$min, $max] = explode('-', $range);
                if ($statusCode >= (int) $min && $statusCode <= (int) $max) {
                    return true;
                }
            } elseif ((int) $range === $statusCode) {
                return true;
            }
        }

        return false;
    }
}

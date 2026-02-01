<?php

namespace Nadi\Yii2;

use yii\base\Component;

/**
 * Nadi application component for Yii 2.
 *
 * Configure in your application config:
 *
 * 'components' => [
 *     'nadi' => [
 *         'class' => \Nadi\Yii2\NadiComponent::class,
 *         'enabled' => true,
 *         'driver' => 'http',
 *         'connections' => [
 *             'http' => [
 *                 'api_key' => 'your-api-key',
 *                 'app_key' => 'your-app-key',
 *             ],
 *         ],
 *     ],
 * ],
 */
class NadiComponent extends Component
{
    public bool $enabled = true;

    public string $driver = 'log';

    public array $connections = [];

    public array $query = [
        'slow_threshold' => 500,
    ];

    public array $http = [
        'hidden_request_headers' => ['Authorization', 'php-auth-pw'],
        'hidden_parameters' => ['password', 'password_confirmation'],
        'ignored_status_codes' => ['200-307'],
    ];

    public array $sampling = [
        'strategy' => 'fixed_rate',
        'config' => [
            'sampling_rate' => 0.1,
        ],
    ];

    private ?Nadi $nadi = null;

    public function init(): void
    {
        parent::init();

        if (empty($this->connections)) {
            $this->connections = [
                'log' => [
                    'path' => \Yii::getAlias('@runtime') . '/nadi',
                ],
                'http' => [
                    'api_key' => '',
                    'app_key' => '',
                    'endpoint' => 'https://nadi.pro/api',
                    'version' => 'v1',
                ],
                'opentelemetry' => [
                    'endpoint' => 'http://localhost:4318',
                    'service_name' => \Yii::$app->name ?? 'yii2-app',
                    'service_version' => '1.0.0',
                    'environment' => YII_ENV,
                ],
            ];
        }

        $this->nadi = new Nadi($this->getConfigArray());
    }

    public function getNadi(): Nadi
    {
        if (! $this->nadi) {
            $this->nadi = new Nadi($this->getConfigArray());
        }

        return $this->nadi;
    }

    public function getConfigArray(): array
    {
        return [
            'enabled' => $this->enabled,
            'driver' => $this->driver,
            'connections' => $this->connections,
            'query' => $this->query,
            'http' => $this->http,
            'sampling' => $this->sampling,
        ];
    }
}

<?php

namespace Nadi\Yii2;

use Nadi\Data\Entry;
use Nadi\Data\ExceptionEntry;
use Nadi\Data\Type;
use Nadi\Transporter\Service;

class Nadi
{
    private static ?Nadi $instance = null;

    private ?Service $service = null;

    private Transporter $transporter;

    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->transporter = new Transporter($this->config);
        $this->service = $this->transporter->getService();
        static::$instance = $this;
    }

    public static function getInstance(): ?self
    {
        if (static::$instance) {
            return static::$instance;
        }

        // Try to get from Yii application component
        if (class_exists(\Yii::class) && \Yii::$app && \Yii::$app->has('nadi')) {
            $component = \Yii::$app->get('nadi');
            if ($component instanceof NadiComponent) {
                return $component->getNadi();
            }
        }

        return null;
    }

    public function isEnabled(): bool
    {
        return ($this->config['enabled'] ?? false) && $this->service !== null;
    }

    public function getTransporter(): ?Service
    {
        return $this->service;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function store(Entry|ExceptionEntry $entry): void
    {
        if (! $this->isEnabled() || ! $this->service) {
            return;
        }

        $this->service->handle($entry->toArray());
    }

    public function recordException(\Throwable $exception): void
    {
        $entry = new \Nadi\Yii2\Data\ExceptionEntry($exception);
        $this->store($entry);
    }

    public function recordQuery(string $sql, float $duration, string $connectionName = 'default'): void
    {
        $entry = new \Nadi\Yii2\Data\Entry(Type::QUERY);
        $entry->content = [
            'connection' => $connectionName,
            'sql' => $sql,
            'duration' => $duration,
            'slow' => $duration >= ($this->config['query']['slow_threshold'] ?? 500),
        ];
        $this->store($entry);
    }

    public function send(): void
    {
        if ($this->service) {
            $this->service->send();
        }
    }

    public function __destruct()
    {
        $this->send();
    }
}

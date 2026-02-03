<?php

namespace Nadi\Yii2;

use Nadi\Sampling\Config as SamplingConfig;
use Nadi\Sampling\DynamicRateSampling;
use Nadi\Sampling\FixedRateSampling;
use Nadi\Sampling\IntervalSampling;
use Nadi\Sampling\PeakLoadSampling;
use Nadi\Sampling\SamplingManager;
use Nadi\Transporter\Contract;
use Nadi\Transporter\Service;

class Transporter
{
    private ?Service $service = null;

    public function __construct(
        private array $config,
    ) {
        if ($this->config['enabled'] ?? false) {
            $this->configure();
        }
    }

    protected function configure(): void
    {
        $transporter = $this->configureTransporter();

        if (! $transporter) {
            return;
        }

        $samplingManager = $this->configureSampling();
        $this->service = new Service($transporter, $samplingManager);
    }

    protected function configureTransporter(): ?Contract
    {
        $driver = $this->config['driver'] ?? 'log';
        $connections = $this->config['connections'] ?? [];
        $driverConfig = $connections[$driver] ?? [];

        $driverClass = match ($driver) {
            'http' => \Nadi\Transporter\Http::class,
            'log' => \Nadi\Transporter\Log::class,
            'opentelemetry' => \Nadi\Transporter\OpenTelemetry::class,
            default => null,
        };

        if (! $driverClass) {
            return null;
        }

        $transporter = new $driverClass;
        $transporter->configure($driverConfig);

        return $transporter;
    }

    protected function configureSampling(): SamplingManager
    {
        $samplingConfig = $this->config['sampling'] ?? [];
        $strategy = $samplingConfig['strategy'] ?? 'fixed_rate';
        $strategyConfig = $samplingConfig['config'] ?? [];

        $sampling = match ($strategy) {
            'fixed_rate' => new FixedRateSampling(new SamplingConfig(
                samplingRate: $strategyConfig['sampling_rate'] ?? 0.1,
            )),
            'dynamic_rate' => new DynamicRateSampling(new SamplingConfig(
                baseRate: $strategyConfig['base_rate'] ?? 0.05,
                loadFactor: $strategyConfig['load_factor'] ?? 1.0,
            )),
            'interval' => new IntervalSampling(new SamplingConfig(
                intervalSeconds: $strategyConfig['interval_seconds'] ?? 60,
            )),
            'peak_load' => new PeakLoadSampling(new SamplingConfig(
                baseRate: $strategyConfig['base_rate'] ?? 0.05,
                loadFactor: $strategyConfig['load_factor'] ?? 1.0,
            )),
            default => new FixedRateSampling(new SamplingConfig(samplingRate: 0.1)),
        };

        return new SamplingManager($sampling);
    }

    public function getService(): ?Service
    {
        return $this->service;
    }
}

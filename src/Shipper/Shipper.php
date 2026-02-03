<?php

namespace Nadi\Yii2\Shipper;

use Nadi\Shipper\BinaryManager;

class Shipper
{
    private BinaryManager $binaryManager;

    public function __construct(
        private string $rootPath,
    ) {
        $this->binaryManager = new BinaryManager(
            $this->rootPath.'/vendor/bin',
        );
    }

    public function install(): void
    {
        $this->binaryManager->install();
    }

    public function isInstalled(): bool
    {
        return $this->binaryManager->isInstalled();
    }

    public function send(string $configPath): array
    {
        return $this->binaryManager->execute([
            'send',
            '--config',
            $configPath,
        ]);
    }

    public function test(string $configPath): array
    {
        return $this->binaryManager->execute([
            'test',
            '--config',
            $configPath,
        ]);
    }

    public function verify(string $configPath): array
    {
        return $this->binaryManager->execute([
            'verify',
            '--config',
            $configPath,
        ]);
    }

    public function getBinaryManager(): BinaryManager
    {
        return $this->binaryManager;
    }
}

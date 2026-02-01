<?php

namespace Nadi\Yii2\Metric;

use Nadi\Metric\Base;

class Network extends Base
{
    public function metrics(): array
    {
        return [
            'net.host.name' => gethostname() ?: 'unknown',
            'net.host.port' => $_SERVER['SERVER_PORT'] ?? null,
            'net.protocol.name' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http',
        ];
    }
}

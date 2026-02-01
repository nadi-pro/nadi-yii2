<?php

namespace Nadi\Yii2\Concerns;

use Nadi\Yii2\Metric\Application;
use Nadi\Yii2\Metric\Framework;
use Nadi\Yii2\Metric\Http;
use Nadi\Yii2\Metric\Network;

trait InteractsWithMetric
{
    public function registerMetrics(): void
    {
        if (method_exists($this, 'addMetric')) {
            $this->addMetric(new Http);
            $this->addMetric(new Framework);
            $this->addMetric(new Application);
            $this->addMetric(new Network);
        }
    }
}

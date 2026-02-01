<?php

namespace Nadi\Yii2\Metric;

use Nadi\Metric\Base;

class Framework extends Base
{
    public function metrics(): array
    {
        $version = 'unknown';
        if (class_exists(\Yii::class)) {
            $version = \Yii::getVersion();
        }

        $appName = 'yii2-app';
        if (class_exists(\Yii::class) && \Yii::$app) {
            $appName = \Yii::$app->name ?? 'yii2-app';
        }

        return [
            'framework.name' => 'yii2',
            'framework.version' => $version,
            'service.name' => $appName,
            'service.version' => '1.0.0',
        ];
    }
}

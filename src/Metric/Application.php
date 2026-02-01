<?php

namespace Nadi\Yii2\Metric;

use Nadi\Metric\Base;

class Application extends Base
{
    public function metrics(): array
    {
        $metrics = [
            'app.environment' => defined('YII_ENV') ? YII_ENV : 'production',
            'app.debug' => defined('YII_DEBUG') ? YII_DEBUG : false,
        ];

        if (class_exists(\Yii::class) && \Yii::$app) {
            $metrics['app.base_path'] = \Yii::$app->basePath;
            $metrics['app.name'] = \Yii::$app->name;

            if (\Yii::$app instanceof \yii\web\Application && \Yii::$app->requestedRoute) {
                $metrics['app.route'] = \Yii::$app->requestedRoute;
            }

            if (\Yii::$app->controller) {
                $metrics['app.controller'] = get_class(\Yii::$app->controller);
                if (\Yii::$app->controller->action) {
                    $metrics['app.action'] = \Yii::$app->controller->action->id;
                }
            }
        }

        return $metrics;
    }
}

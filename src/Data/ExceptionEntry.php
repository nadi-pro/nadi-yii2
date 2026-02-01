<?php

namespace Nadi\Yii2\Data;

use Nadi\Data\ExceptionEntry as CoreExceptionEntry;
use Nadi\Yii2\Concerns\InteractsWithMetric;

class ExceptionEntry extends CoreExceptionEntry
{
    use InteractsWithMetric;

    public function __construct(\Throwable $exception)
    {
        parent::__construct($exception);
        $this->registerMetrics();
        $this->captureUser();
    }

    protected function captureUser(): void
    {
        try {
            if (class_exists(\Yii::class) && \Yii::$app && isset(\Yii::$app->user) && ! \Yii::$app->user->isGuest) {
                $identity = \Yii::$app->user->identity;
                if ($identity) {
                    $this->user = [
                        'id' => $identity->getId(),
                        'name' => method_exists($identity, 'getName') ? $identity->getName() : null,
                        'email' => $identity->email ?? null,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Silently fail if user is not available
        }
    }
}

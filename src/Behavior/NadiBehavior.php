<?php

namespace Nadi\Yii2\Behavior;

use Nadi\Yii2\Handler\HandleHttpRequestEvent;
use Nadi\Yii2\Nadi;
use yii\base\Application;
use yii\base\Behavior;
use yii\base\Event;

/**
 * NadiBehavior attaches to the Application to monitor lifecycle events.
 *
 * Attach to your application:
 * 'as nadi' => [
 *     'class' => \Nadi\Yii2\Behavior\NadiBehavior::class,
 * ],
 */
class NadiBehavior extends Behavior
{
    public function events(): array
    {
        return [
            Application::EVENT_AFTER_REQUEST => 'afterRequest',
        ];
    }

    public function afterRequest(Event $event): void
    {
        $nadi = Nadi::getInstance();
        if (! $nadi || ! $nadi->isEnabled()) {
            return;
        }

        $app = $event->sender;
        if (! $app instanceof \yii\web\Application) {
            return;
        }

        $handler = new HandleHttpRequestEvent(
            $nadi,
            $nadi->getConfig(),
        );

        $handler->handle($app->request, $app->response);
        $nadi->send();
    }
}

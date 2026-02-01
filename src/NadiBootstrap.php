<?php

namespace Nadi\Yii2;

use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * Bootstrap class for Nadi monitoring.
 *
 * Add to your application config:
 *
 * 'bootstrap' => ['nadi'],
 * 'components' => [
 *     'nadi' => [
 *         'class' => \Nadi\Yii2\NadiComponent::class,
 *         'enabled' => true,
 *         'driver' => 'http',
 *         // ... other config
 *     ],
 * ],
 */
class NadiBootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        if (! $app->has('nadi')) {
            return;
        }

        /** @var NadiComponent $component */
        $component = $app->get('nadi');

        if (! $component->enabled) {
            return;
        }

        // Register exception handler
        $this->registerExceptionHandler($app, $component);

        // Register HTTP lifecycle events
        $this->registerHttpEvents($app, $component);

        // Register query monitoring
        $this->registerQueryMonitoring($component);

        // Register application shutdown
        $app->on(Application::EVENT_AFTER_REQUEST, function () use ($component) {
            $component->getNadi()->send();
        });
    }

    protected function registerExceptionHandler(Application $app, NadiComponent $component): void
    {
        $originalHandler = set_exception_handler(null);
        restore_exception_handler();

        set_exception_handler(function (\Throwable $exception) use ($component, $originalHandler) {
            try {
                $handler = new Handler\HandleExceptionEvent($component->getNadi());
                $handler->handle($exception);
            } catch (\Throwable $e) {
                // Silently fail to avoid recursive errors
            }

            if ($originalHandler) {
                call_user_func($originalHandler, $exception);
            } else {
                throw $exception;
            }
        });
    }

    protected function registerHttpEvents(Application $app, NadiComponent $component): void
    {
        if ($app instanceof \yii\web\Application) {
            $app->on(Application::EVENT_AFTER_REQUEST, function () use ($app, $component) {
                $handler = new Handler\HandleHttpRequestEvent(
                    $component->getNadi(),
                    $component->getConfigArray(),
                );
                $handler->handle($app->request, $app->response);
            });
        }
    }

    protected function registerQueryMonitoring(NadiComponent $component): void
    {
        if (! class_exists(\yii\db\Connection::class)) {
            return;
        }

        \yii\base\Event::on(
            \yii\db\Command::class,
            \yii\db\Command::EVENT_AFTER_EXECUTE,
            function ($event) use ($component) {
                /** @var \yii\db\CommandEvent $event */
                if (! isset($event->sender)) {
                    return;
                }

                $duration = $event->sender->pdoStatement
                    ? (microtime(true) - ($event->sender->_startTime ?? microtime(true))) * 1000
                    : 0;

                $handler = new Handler\HandleQueryEvent($component->getNadi());
                $handler->handle(
                    $event->sender->getRawSql(),
                    $duration,
                );
            },
        );
    }
}

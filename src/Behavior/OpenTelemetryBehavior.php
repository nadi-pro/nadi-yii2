<?php

namespace Nadi\Yii2\Behavior;

use Nadi\Yii2\Nadi;
use yii\base\Application;
use yii\base\Behavior;
use yii\base\Event;

/**
 * OpenTelemetryBehavior handles trace context propagation.
 *
 * Attach to your application:
 * 'as otel' => [
 *     'class' => \Nadi\Yii2\Behavior\OpenTelemetryBehavior::class,
 * ],
 */
class OpenTelemetryBehavior extends Behavior
{
    public function events(): array
    {
        return [
            Application::EVENT_BEFORE_REQUEST => 'beforeRequest',
            Application::EVENT_AFTER_REQUEST => 'afterRequest',
        ];
    }

    public function beforeRequest(Event $event): void
    {
        $nadi = Nadi::getInstance();
        if (! $nadi || ! $nadi->isEnabled()) {
            return;
        }

        $app = $event->sender;
        if (! $app instanceof \yii\web\Application) {
            return;
        }

        // Extract trace context from incoming request
        $traceparent = $app->request->headers->get('traceparent');
        if ($traceparent) {
            $this->extractTraceContext($traceparent);
        }
    }

    public function afterRequest(Event $event): void
    {
        // Inject trace context into response if available
        $app = $event->sender;
        if (! $app instanceof \yii\web\Application) {
            return;
        }

        $traceparent = $app->request->headers->get('traceparent');
        if ($traceparent) {
            $context = $this->extractTraceContext($traceparent);
            if ($context && isset($context['trace_id'], $context['span_id'])) {
                $app->response->headers->set('traceparent', sprintf(
                    '00-%s-%s-01',
                    $context['trace_id'],
                    $context['span_id'],
                ));
            }
        }
    }

    protected function extractTraceContext(string $traceparent): ?array
    {
        $parts = explode('-', $traceparent);

        if (count($parts) < 4) {
            return null;
        }

        return [
            'version' => $parts[0],
            'trace_id' => $parts[1],
            'span_id' => $parts[2],
            'trace_flags' => $parts[3],
        ];
    }
}

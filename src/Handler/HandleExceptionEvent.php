<?php

namespace Nadi\Yii2\Handler;

use Nadi\Yii2\Actions\ExceptionContext;
use Nadi\Yii2\Data\ExceptionEntry;

class HandleExceptionEvent extends Base
{
    public function handle(\Throwable $exception): void
    {
        if (! $this->nadi->isEnabled()) {
            return;
        }

        $entry = new ExceptionEntry($exception);

        $entry->content = [
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $this->formatTrace($exception),
            'context' => ExceptionContext::get($exception),
        ];

        $entry->hashFamily = $this->hash(
            get_class($exception).
            $exception->getFile().
            $exception->getLine().
            $exception->getMessage().
            date('Y-m-d'),
        );

        $this->store($entry->toArray());
    }

    protected function formatTrace(\Throwable $exception): array
    {
        return array_map(function ($frame) {
            return [
                'file' => $frame['file'] ?? '[internal]',
                'line' => $frame['line'] ?? 0,
                'function' => $frame['function'] ?? '',
                'class' => $frame['class'] ?? '',
                'type' => $frame['type'] ?? '',
            ];
        }, $exception->getTrace());
    }
}

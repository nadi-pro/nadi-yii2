<?php

namespace Nadi\Yii2\Concerns;

trait FetchesStackTrace
{
    protected function getCallerFromStackTrace(int $forgetLines = 0): ?array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $trace = array_slice($trace, $forgetLines);

        foreach ($trace as $frame) {
            if (! isset($frame['file'])) {
                continue;
            }

            foreach ($this->ignoredPaths() as $path) {
                if (str_contains($frame['file'], $path)) {
                    continue 2;
                }
            }

            return $frame;
        }

        return null;
    }

    protected function ignoredPaths(): array
    {
        $paths = [
            DIRECTORY_SEPARATOR.'vendor',
        ];

        if (class_exists(\Yii::class)) {
            $vendorPath = \Yii::getAlias('@vendor', false);
            if ($vendorPath) {
                $paths[] = $vendorPath;
            }
        }

        return $paths;
    }
}

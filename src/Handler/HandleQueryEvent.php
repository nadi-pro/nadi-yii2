<?php

namespace Nadi\Yii2\Handler;

use Nadi\Data\Type;
use Nadi\Yii2\Concerns\FetchesStackTrace;
use Nadi\Yii2\Data\Entry;
use Nadi\Yii2\Support\OpenTelemetrySemanticConventions;

class HandleQueryEvent extends Base
{
    use FetchesStackTrace;

    public function handle(string $sql, float $duration, string $connectionName = 'default'): void
    {
        if (! $this->nadi->isEnabled()) {
            return;
        }

        $config = $this->nadi->getConfig();
        $slowThreshold = $config['query']['slow_threshold'] ?? 500;

        if ($duration < $slowThreshold) {
            return;
        }

        $entry = new Entry(Type::QUERY);

        $caller = $this->getCallerFromStackTrace(6);

        $entry->content = [
            'connection' => $connectionName,
            'sql' => $sql,
            'duration' => $duration,
            'slow' => true,
            'file' => $caller['file'] ?? '',
            'line' => $caller['line'] ?? 0,
        ];

        $entry->content = array_merge(
            $entry->content,
            OpenTelemetrySemanticConventions::databaseAttributes($connectionName, $sql, $duration),
        );

        $this->store($entry->toArray());
    }
}

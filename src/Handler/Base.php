<?php

namespace Nadi\Yii2\Handler;

use Nadi\Yii2\Nadi;

abstract class Base
{
    public function __construct(
        protected Nadi $nadi,
    ) {}

    protected function store(array $data): void
    {
        $transporter = $this->nadi->getTransporter();

        if (! $transporter) {
            return;
        }

        $transporter->handle($data);
    }

    protected function hash(string $value): string
    {
        return sha1($value);
    }
}

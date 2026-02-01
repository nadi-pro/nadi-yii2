<?php

namespace Nadi\Yii2\Metric;

use Nadi\Metric\Base;
use Nadi\Yii2\Support\OpenTelemetrySemanticConventions;

class Http extends Base
{
    public function metrics(): array
    {
        return OpenTelemetrySemanticConventions::httpAttributesFromGlobals();
    }
}

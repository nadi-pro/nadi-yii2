<?php

namespace Nadi\Yii2\Tests\Feature;

use Nadi\Yii2\NadiBootstrap;
use Nadi\Yii2\Tests\TestCase;

class BootstrapTest extends TestCase
{
    public function test_bootstrap_can_be_instantiated(): void
    {
        $bootstrap = new NadiBootstrap;

        $this->assertInstanceOf(NadiBootstrap::class, $bootstrap);
    }
}

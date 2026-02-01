<?php

namespace Nadi\Yii2\Tests\Feature;

use Nadi\Yii2\Nadi;
use Nadi\Yii2\Tests\TestCase;

class ComponentTest extends TestCase
{
    public function test_nadi_can_be_instantiated(): void
    {
        $config = $this->getNadiConfig();
        $nadi = new Nadi($config);

        $this->assertInstanceOf(Nadi::class, $nadi);
        $this->assertTrue($nadi->isEnabled());
    }

    public function test_nadi_is_disabled_when_configured(): void
    {
        $config = $this->getNadiConfig(['enabled' => false]);
        $nadi = new Nadi($config);

        $this->assertFalse($nadi->isEnabled());
    }

    public function test_nadi_returns_config(): void
    {
        $config = $this->getNadiConfig();
        $nadi = new Nadi($config);

        $this->assertEquals($config, $nadi->getConfig());
    }

    public function test_nadi_singleton_returns_instance(): void
    {
        $config = $this->getNadiConfig();
        $nadi = new Nadi($config);

        $this->assertSame($nadi, Nadi::getInstance());
    }
}

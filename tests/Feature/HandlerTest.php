<?php

namespace Nadi\Yii2\Tests\Feature;

use Nadi\Yii2\Handler\HandleExceptionEvent;
use Nadi\Yii2\Handler\HandleQueryEvent;
use Nadi\Yii2\Nadi;
use Nadi\Yii2\Tests\TestCase;

class HandlerTest extends TestCase
{
    public function test_exception_handler_can_be_instantiated(): void
    {
        $config = $this->getNadiConfig();
        $nadi = new Nadi($config);

        $handler = new HandleExceptionEvent($nadi);

        $this->assertInstanceOf(HandleExceptionEvent::class, $handler);
    }

    public function test_query_handler_can_be_instantiated(): void
    {
        $config = $this->getNadiConfig();
        $nadi = new Nadi($config);

        $handler = new HandleQueryEvent($nadi);

        $this->assertInstanceOf(HandleQueryEvent::class, $handler);
    }
}

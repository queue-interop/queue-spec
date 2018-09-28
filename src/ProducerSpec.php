<?php

namespace Interop\Queue\Spec;

use Interop\Queue\CompletionListener;
use Interop\Queue\Message;
use Interop\Queue\Producer;
use PHPUnit\Framework\TestCase;

abstract class ProducerSpec extends TestCase
{
    public function testShouldImplementContextInterface()
    {
        $this->assertInstanceOf(Producer::class, $this->createProducer());
    }

    /**
     * @return Producer
     */
    abstract protected function createProducer();
}

<?php

namespace Interop\Queue\Spec;

use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProducer;
use PHPUnit\Framework\TestCase;

abstract class PsrProducerSpec extends TestCase
{
    public function testShouldImplementPsrContextInterface()
    {
        $this->assertInstanceOf(PsrProducer::class, $this->createProducer());
    }

    public function testShouldReturnDefaultDeliveryDelayIfNotPreviouslySet()
    {
        $producer = $this->createProducer();

        $this->assertSame(PsrMessage::DEFAULT_DELIVERY_DELAY, $producer->getDeliveryDelay());
    }

    public function testShouldReturnPreviouslySetDeliveryDelay()
    {
        $producer = $this->createProducer();

        $producer->setDeliveryDelay(123.45);

        $this->assertSame(123.45, $producer->getDeliveryDelay());
    }

    /**
     * @return PsrProducer
     */
    abstract protected function createProducer();
}

<?php

namespace Interop\Queue\Spec;

use Interop\Queue\CompletionListener;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProducer;
use PHPUnit\Framework\TestCase;

abstract class PsrProducerSpec extends TestCase
{
    public function testShouldImplementPsrContextInterface()
    {
        $this->assertInstanceOf(PsrProducer::class, $this->createProducer());
    }

    public function testShouldReturnNullIfCompletionListenerWasNotPreviouslySet()
    {
        $producer = $this->createProducer();

        $this->assertNull($producer->getCompletionListener());
    }

    public function testShouldReturnPreviouslySetCompletionListener()
    {
        $completionListener = $this->createMock(CompletionListener::class);

        $producer = $this->createProducer();
        $producer->setCompletionListener($completionListener);

        $this->assertSame($completionListener, $producer->getCompletionListener());
    }

    public function testShouldAllowResetPreviouslySetCompletionListener()
    {
        $producer = $this->createProducer();

        $producer->setCompletionListener($this->createMock(CompletionListener::class));

        // guard
        $this->assertNotNull($producer->getCompletionListener());

        $producer->setCompletionListener(null);

        $this->assertNull($producer->getCompletionListener());
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

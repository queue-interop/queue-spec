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

    /**
     * @return PsrProducer
     */
    abstract protected function createProducer();
}

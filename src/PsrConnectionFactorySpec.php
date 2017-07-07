<?php

namespace Interop\Queue\Spec;

use Interop\Queue\PsrConnectionFactory;
use Interop\Queue\PsrContext;
use PHPUnit\Framework\TestCase;

abstract class PsrConnectionFactorySpec extends TestCase
{
    public function testShouldImplementPsrConnectionFactoryInterface()
    {
        $this->assertInstanceOf(PsrConnectionFactory::class, $this->createConnectionFactory());
    }

    public function testShouldReturnContextOnCreateContextMethodCall()
    {
        $factory = $this->createConnectionFactory();

        $this->assertInstanceOf(PsrContext::class, $factory->createContext());
    }

    /**
     * @return PsrConnectionFactory
     */
    abstract protected function createConnectionFactory();
}

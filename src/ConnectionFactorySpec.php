<?php

namespace Interop\Queue\Spec;

use Interop\Queue\ConnectionFactory;
use Interop\Queue\Context;
use PHPUnit\Framework\TestCase;

abstract class ConnectionFactorySpec extends TestCase
{
    public function testShouldImplementConnectionFactoryInterface()
    {
        $this->assertInstanceOf(ConnectionFactory::class, $this->createConnectionFactory());
    }

    public function testShouldReturnContextOnCreateContextMethodCall()
    {
        $factory = $this->createConnectionFactory();

        $this->assertInstanceOf(Context::class, $factory->createContext());
    }

    /**
     * @return ConnectionFactory
     */
    abstract protected function createConnectionFactory();
}

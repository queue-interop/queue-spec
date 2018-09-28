<?php

namespace Interop\Queue\Spec;

use Interop\Queue\Queue;
use PHPUnit\Framework\TestCase;

abstract class QueueSpec extends TestCase
{
    const EXPECTED_QUEUE_NAME = 'theQueueName';

    public function testShouldImplementQueueInterface()
    {
        $this->assertInstanceOf(Queue::class, $this->createQueue());
    }

    public function testShouldReturnQueueName()
    {
        $queue = $this->createQueue();

        $this->assertSame(self::EXPECTED_QUEUE_NAME, $queue->getQueueName());
    }

    /**
     * @return Queue
     */
    abstract protected function createQueue();
}

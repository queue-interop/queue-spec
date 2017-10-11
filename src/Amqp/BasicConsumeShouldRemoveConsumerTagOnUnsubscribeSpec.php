<?php

namespace Interop\Queue\Spec\Amqp;

use Interop\Amqp\AmqpConsumer;
use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpMessage;
use Interop\Amqp\AmqpQueue;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class BasicConsumeShouldRemoveConsumerTagOnUnsubscribeSpec extends TestCase
{
    /**
     * @var AmqpContext
     */
    private $context;

    public function tearDown()
    {
        if ($this->context) {
            $this->context->close();
        }

        parent::tearDown();
    }

    public function test()
    {
        $this->context = $context = $this->createContext();
        $queue = $this->createQueue($context, 'basic_consume_should_remove_consumer_tag_on_unsubscribe_spec');

        $consumer = $context->createConsumer($queue);

        $context->basicConsumeSubscribe($consumer, function() {});
        $context->basicConsume(100);

        // guard
        $this->assertNotEmpty($consumer->getConsumerTag());

        $context->basicConsumeUnsubscribe($consumer);

        $this->assertEmpty($consumer->getConsumerTag());
    }

    /**
     * @return AmqpContext
     */
    abstract protected function createContext();

    /**
     * @param AmqpContext $context
     * @param string      $queueName
     *
     * @return AmqpQueue
     */
    protected function createQueue(AmqpContext $context, $queueName)
    {
        $queue = $context->createQueue($queueName);
        $context->declareQueue($queue);
        $context->purgeQueue($queue);

        return $queue;
    }
}

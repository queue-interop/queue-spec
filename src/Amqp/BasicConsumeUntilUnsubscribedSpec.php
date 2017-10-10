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
abstract class BasicConsumeUntilUnsubscribedSpec extends TestCase
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
        $fooQueue = $this->createQueue($context, 'foo_basic_consume_until_unsubscribed_spec');
        $barQueue = $this->createQueue($context, 'bar_basic_consume_until_unsubscribed_spec');

        $context->createProducer()->send($fooQueue, $context->createMessage());
        $context->createProducer()->send($barQueue, $context->createMessage());

        $fooConsumer = $context->createConsumer($fooQueue);
        $barConsumer = $context->createConsumer($barQueue);

        $consumedMessages = 0;
        $callback = function(AmqpMessage $message, AmqpConsumer $consumer) use (&$consumedMessages) {
            $consumedMessages++;

            $consumer->acknowledge($message);

            return true;
        };

        $context->basicConsumeSubscribe($fooConsumer, $callback);
        $context->basicConsumeSubscribe($barConsumer, $callback);
        $context->basicConsume(1000);

        $this->assertEquals(2, $consumedMessages);

        $context->createProducer()->send($fooQueue, $context->createMessage());
        $context->createProducer()->send($barQueue, $context->createMessage());

        $consumedMessages = 0;
        $context->basicConsumeUnsubscribe($fooConsumer);
        $context->basicConsume(1000);

        $this->assertEquals(1, $consumedMessages);
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

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
abstract class PreFetchCountSpec extends TestCase
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
        $queue = $this->createQueue($context, 'pre_fetch_count_spec');

        $context->createProducer()->send($queue, $context->createMessage());
        $context->createProducer()->send($queue, $context->createMessage());
        $context->createProducer()->send($queue, $context->createMessage());
        $context->createProducer()->send($queue, $context->createMessage());
        $context->createProducer()->send($queue, $context->createMessage());

        $this->context->setQos(0, 3, false);

        $consumer = $context->createConsumer($queue);

        $consumedMessages = 0;
        $context->subscribe($consumer, function() use (&$consumedMessages) {
            $consumedMessages++;
        });
        $context->consume(100);

        $this->assertEquals(3, $consumedMessages);
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

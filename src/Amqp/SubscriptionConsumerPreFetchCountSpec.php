<?php

namespace Interop\Queue\Spec\Amqp;

use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpQueue;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SubscriptionConsumerPreFetchCountSpec extends TestCase
{
    /**
     * @var AmqpContext
     */
    private $context;

    protected function tearDown(): void
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

        $subscriptionConsumer = $context->createSubscriptionConsumer();

        $consumedMessages = 0;
        $subscriptionConsumer->subscribe($consumer, function() use (&$consumedMessages) {
            $consumedMessages++;
        });
        $subscriptionConsumer->consume(100);

        $this->assertEquals(3, $consumedMessages);
    }

    abstract protected function createContext(): AmqpContext;

    protected function createQueue(AmqpContext $context, string $queueName): AmqpQueue
    {
        $queue = $context->createQueue($queueName);
        $context->declareQueue($queue);
        $context->purgeQueue($queue);

        return $queue;
    }
}

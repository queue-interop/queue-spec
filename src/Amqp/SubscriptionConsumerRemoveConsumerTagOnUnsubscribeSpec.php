<?php

namespace Interop\Queue\Spec\Amqp;

use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpQueue;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SubscriptionConsumerRemoveConsumerTagOnUnsubscribeSpec extends TestCase
{
    /**
     * @var AmqpContext
     */
    private $context;

    protected function tearDown()
    {
        if ($this->context) {
            $this->context->close();
        }

        parent::tearDown();
    }

    public function test()
    {
        $this->context = $context = $this->createContext();
        $context->setQos(0, 5, false);

        $queue = $this->createQueue($context, 'basic_consume_should_remove_consumer_tag_on_unsubscribe_spec');

        $consumer = $context->createConsumer($queue);

        $subscriptionConsumer = $context->createSubscriptionConsumer();
        $subscriptionConsumer->subscribe($consumer, function() {});
        $subscriptionConsumer->consume(100);

        // guard
        $this->assertNotEmpty($consumer->getConsumerTag());

        $subscriptionConsumer->unsubscribe($consumer);

        $this->assertEmpty($consumer->getConsumerTag());
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

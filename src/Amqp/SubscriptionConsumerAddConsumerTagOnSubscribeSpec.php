<?php

namespace Interop\Queue\Spec\Amqp;

use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpQueue;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SubscriptionConsumerAddConsumerTagOnSubscribeSpec extends TestCase
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
        $context->setQos(0, 5, false);

        $queue = $this->createQueue($context, 'amqp_subscription_consumer_add_consumer_tag_on_subscribe_spec');

        $consumer = $context->createConsumer($queue);

        //guard
        $this->assertNull($consumer->getConsumerTag());

        $subscriptionConsumer = $context->createSubscriptionConsumer();

        $subscriptionConsumer->subscribe($consumer, function() {});

        $this->assertNotEmpty($consumer->getConsumerTag());
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

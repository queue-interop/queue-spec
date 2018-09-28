<?php

namespace Interop\Queue\Spec;

use Interop\Queue\Consumer;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Queue;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SubscriptionConsumerConsumeFromAllSubscribedQueuesSpec extends TestCase
{
    /**
     * @var Context
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

        $fooQueue = $this->createQueue($context, 'foo_subscription_consumer_consume_from_all_subscribed_queues_spec');
        $barQueue = $this->createQueue($context, 'bar_subscription_consumer_consume_from_all_subscribed_queues_spec');

        $expectedFooBody = 'fooBody';
        $expectedBarBody = 'barBody';

        $context->createProducer()->send($fooQueue, $context->createMessage($expectedFooBody));
        $context->createProducer()->send($barQueue, $context->createMessage($expectedBarBody));

        $fooConsumer = $context->createConsumer($fooQueue);
        $barConsumer = $context->createConsumer($barQueue);

        $actualBodies = [];
        $actualQueues = [];
        $callback = function(Message $message, Consumer $consumer) use (&$actualBodies, &$actualQueues) {
            $actualBodies[] = $message->getBody();
            $actualQueues[] = $consumer->getQueue()->getQueueName();

            $consumer->acknowledge($message);

            return true;
        };

        $subscriptionConsumer = $context->createSubscriptionConsumer();
        $subscriptionConsumer->subscribe($fooConsumer, $callback);
        $subscriptionConsumer->subscribe($barConsumer, $callback);

        $subscriptionConsumer->consume(1000);

        $this->assertEquals([$expectedFooBody, $expectedBarBody], $actualBodies);
        $this->assertEquals(
            [
                'foo_subscription_consumer_consume_from_all_subscribed_queues_spec',
                'bar_subscription_consumer_consume_from_all_subscribed_queues_spec'
            ],
            $actualQueues
        );
    }

    /**
     * @return Context
     */
    abstract protected function createContext();

    /**
     * @param Context $context
     * @param string     $queueName
     *
     * @return Queue
     */
    protected function createQueue(Context $context, $queueName)
    {
        return $context->createQueue($queueName);
    }
}

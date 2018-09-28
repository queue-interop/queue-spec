<?php

namespace Interop\Queue\Spec;

use Interop\Queue\Consumer;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Queue;
use Interop\Queue\SubscriptionConsumer;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SubscriptionConsumerConsumeUntilUnsubscribedSpec extends TestCase
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var SubscriptionConsumer
     */
    protected $subscriptionConsumer;

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

        $fooQueue = $this->createQueue($context, 'foo_subscription_consumer_consume_until_unsubscribed_spec');
        $barQueue = $this->createQueue($context, 'bar_subscription_consumer_consume_until_unsubscribed_spec');

        $context->createProducer()->send($fooQueue, $context->createMessage());
        $context->createProducer()->send($barQueue, $context->createMessage());

        $fooConsumer = $context->createConsumer($fooQueue);
        $barConsumer = $context->createConsumer($barQueue);

        $consumedMessages = 0;
        $callback = function(Message $message, Consumer $consumer) use (&$consumedMessages) {
            $consumedMessages++;

            $consumer->acknowledge($message);

            return true;
        };

        $this->subscriptionConsumer = $subscriptionConsumer = $context->createSubscriptionConsumer();
        $subscriptionConsumer->subscribe($fooConsumer, $callback);
        $subscriptionConsumer->subscribe($barConsumer, $callback);

        $subscriptionConsumer->consume(1000);

        $this->assertEquals(2, $consumedMessages);

        $subscriptionConsumer->unsubscribe($fooConsumer);

        $context->createProducer()->send($fooQueue, $context->createMessage());
        $context->createProducer()->send($barQueue, $context->createMessage());

        $consumedMessages = 0;
        $subscriptionConsumer->consume(1000);

        $this->assertEquals(1, $consumedMessages);
    }

    /**
     * @return Context
     */
    abstract protected function createContext();

    /**
     * @param Context $context
     * @param string      $queueName
     *
     * @return Queue
     */
    protected function createQueue(Context $context, $queueName)
    {
        return $context->createQueue($queueName);
    }
}

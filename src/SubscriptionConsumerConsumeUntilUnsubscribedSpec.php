<?php

namespace Interop\Queue\Spec;

use Interop\Queue\PsrConsumer;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrQueue;
use Interop\Queue\PsrSubscriptionConsumerAwareContext;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SubscriptionConsumerConsumeUntilUnsubscribedSpec extends TestCase
{
    /**
     * @var PsrContext
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

        $fooQueue = $this->createQueue($context, 'foo_subscription_consumer_consume_until_unsubscribed_spec');
        $barQueue = $this->createQueue($context, 'bar_subscription_consumer_consume_until_unsubscribed_spec');

        $context->createProducer()->send($fooQueue, $context->createMessage());
        $context->createProducer()->send($barQueue, $context->createMessage());

        $fooConsumer = $context->createConsumer($fooQueue);
        $barConsumer = $context->createConsumer($barQueue);

        $consumedMessages = 0;
        $callback = function(PsrMessage $message, PsrConsumer $consumer) use (&$consumedMessages) {
            $consumedMessages++;

            $consumer->acknowledge($message);

            return true;
        };

        $subscriptionConsumer = $context->createSubscriptionConsumer();
        $subscriptionConsumer->subscribe($fooConsumer, $callback);
        $subscriptionConsumer->subscribe($barConsumer, $callback);

        $subscriptionConsumer->consume(1000);

        $this->assertEquals(2, $consumedMessages);

        $context->createProducer()->send($fooQueue, $context->createMessage());
        $context->createProducer()->send($barQueue, $context->createMessage());

        $consumedMessages = 0;
        $subscriptionConsumer->unsubscribe($fooConsumer);
        $subscriptionConsumer->consume(1000);

        $this->assertEquals(1, $consumedMessages);
    }

    /**
     * @return PsrContext|PsrSubscriptionConsumerAwareContext
     */
    abstract protected function createContext();

    /**
     * @param PsrContext $context
     * @param string      $queueName
     *
     * @return PsrQueue
     */
    protected function createQueue(PsrContext $context, $queueName)
    {
        return $context->createQueue($queueName);
    }
}

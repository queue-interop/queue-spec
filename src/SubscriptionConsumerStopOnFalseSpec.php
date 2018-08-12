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
abstract class SubscriptionConsumerStopOnFalseSpec extends TestCase
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

        $fooQueue = $this->createQueue($context, 'foo_subscription_consumer_stop_on_false_spec');
        $barQueue = $this->createQueue($context, 'bar_subscription_consumer_stop_on_false_spec');

        $expectedFooBody = __CLASS__.'foo'.time();
        $expectedBarBody = __CLASS__.'bar'.time();

        $context->createProducer()->send($fooQueue, $context->createMessage($expectedFooBody));
        $context->createProducer()->send($barQueue, $context->createMessage($expectedBarBody));

        $consumedMessages = 0;
        $callback = function(PsrMessage $message, PsrConsumer $consumer) use (&$consumedMessages) {
            $consumedMessages++;

            $consumer->acknowledge($message);

            return false;
        };

        $fooConsumer = $context->createConsumer($fooQueue);
        $barConsumer = $context->createConsumer($barQueue);

        $subscriptionConsumer = $context->createSubscriptionConsumer();
        $subscriptionConsumer->subscribe($fooConsumer, $callback);
        $subscriptionConsumer->subscribe($barConsumer, $callback);

        $subscriptionConsumer->consume(1000);

        $this->assertEquals(1, $consumedMessages);
    }

    /**
     * @return PsrContext|PsrSubscriptionConsumerAwareContext
     */
    abstract protected function createContext();

    /**
     * @param PsrContext $context
     * @param string     $queueName
     *
     * @return PsrQueue
     */
    protected function createQueue(PsrContext $context, $queueName)
    {
        return $context->createQueue($queueName);
    }
}

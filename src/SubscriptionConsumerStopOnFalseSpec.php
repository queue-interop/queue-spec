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
abstract class SubscriptionConsumerStopOnFalseSpec extends TestCase
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

        $fooQueue = $this->createQueue($context, 'foo_subscription_consumer_stop_on_false_spec');
        $barQueue = $this->createQueue($context, 'bar_subscription_consumer_stop_on_false_spec');

        $expectedFooBody = __CLASS__.'foo'.time();
        $expectedBarBody = __CLASS__.'bar'.time();

        $context->createProducer()->send($fooQueue, $context->createMessage($expectedFooBody));
        $context->createProducer()->send($barQueue, $context->createMessage($expectedBarBody));

        $consumedMessages = 0;
        $callback = function(Message $message, Consumer $consumer) use (&$consumedMessages) {
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

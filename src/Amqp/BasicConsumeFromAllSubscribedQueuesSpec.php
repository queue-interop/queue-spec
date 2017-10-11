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
abstract class BasicConsumeFromAllSubscribedQueuesSpec extends TestCase
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
        $fooQueue = $this->createQueue($context, 'foo_basic_consume_from_all_subscribed_queues_spec');
        $barQueue = $this->createQueue($context, 'bar_basic_consume_from_all_subscribed_queues_spec');

        $expectedFooBody = 'fooBody';
        $expectedBarBody = 'barBody';

        $context->createProducer()->send($fooQueue, $context->createMessage($expectedFooBody));
        $context->createProducer()->send($barQueue, $context->createMessage($expectedBarBody));

        $fooConsumer = $context->createConsumer($fooQueue);
        $barConsumer = $context->createConsumer($barQueue);

        $actualBodies = [];
        $actualQueues = [];
        $callback = function(AmqpMessage $message, AmqpConsumer $consumer) use (&$actualBodies, &$actualQueues) {
            $actualBodies[] = $message->getBody();
            $actualQueues[] = $consumer->getQueue()->getQueueName();

            $consumer->acknowledge($message);

            return true;
        };

        $context->subscribe($fooConsumer, $callback);
        $context->subscribe($barConsumer, $callback);
        $context->consume(1000);

        $this->assertEquals([$expectedFooBody, $expectedBarBody], $actualBodies);
        $this->assertEquals(
            [
                'foo_basic_consume_from_all_subscribed_queues_spec',
                'bar_basic_consume_from_all_subscribed_queues_spec'
            ],
            $actualQueues
        );
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

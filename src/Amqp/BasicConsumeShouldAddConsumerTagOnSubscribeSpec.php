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
abstract class BasicConsumeShouldAddConsumerTagOnSubscribeSpec extends TestCase
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
        $context->setQos(0, 5, false);

        $queue = $this->createQueue($context, 'basic_consume_should_add_consumer_tag_on_subscribe_spec');

        $consumer = $context->createConsumer($queue);

        $context->subscribe($consumer, function() {});

        $this->assertNotEmpty($consumer->getConsumerTag());
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

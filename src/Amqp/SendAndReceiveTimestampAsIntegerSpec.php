<?php

namespace Interop\Queue\Spec\Amqp;

use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpMessage;
use Interop\Amqp\AmqpQueue;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SendAndReceiveTimestampAsIntegerSpec extends TestCase
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

        $queue = $this->createQueue($context, 'send_and_receive_timestamp_as_integer_spec');

        $expectedTime = time();
        $expectedBody = __METHOD__.time();

        $message = $context->createMessage($expectedBody);
        $message->setTimestamp($expectedTime);

        $context->createProducer()->send($queue, $message);

        $consumer = $context->createConsumer($queue);

        $receivedMessage = $consumer->receive(100);

        $this->assertInstanceOf(AmqpMessage::class, $receivedMessage);
        $this->assertSame($expectedBody, $receivedMessage->getBody());
        $this->assertSame($expectedTime, $receivedMessage->getTimestamp());
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

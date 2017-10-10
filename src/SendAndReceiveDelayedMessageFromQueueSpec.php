<?php

namespace Interop\Queue\Spec;

use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrQueue;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SendAndReceiveDelayedMessageFromQueueSpec extends TestCase
{
    public function test()
    {
        $context = $this->createContext();
        $queue = $this->createQueue($context, 'send_and_receive_delayed_message_from_queue_spec');

        $consumer = $context->createConsumer($queue);

        // guard
        $this->assertNull($consumer->receiveNoWait());

        $expectedBody = __CLASS__.time();

        $producer = $context->createProducer();
        $producer->setDeliveryDelay(5000); // 5sec
        $producer->send($queue, $context->createMessage($expectedBody));

        $sendAt = microtime(true);

        $message = $consumer->receive(8000); // 8 sec

        $this->assertInstanceOf(PsrMessage::class, $message);
        $consumer->acknowledge($message);
        $this->assertSame($expectedBody, $message->getBody());

        $this->assertGreaterThanOrEqual(4, microtime(true) - $sendAt);
    }

    /**
     * @return PsrContext
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

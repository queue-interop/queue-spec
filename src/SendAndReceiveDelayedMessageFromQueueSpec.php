<?php

namespace Interop\Queue\Spec;

use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Queue;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SendAndReceiveDelayedMessageFromQueueSpec extends TestCase
{
    /**
     * @var Context
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

        $this->assertInstanceOf(Message::class, $message);
        $consumer->acknowledge($message);
        $this->assertSame($expectedBody, $message->getBody());

        $this->assertGreaterThanOrEqual(4, microtime(true) - $sendAt);
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

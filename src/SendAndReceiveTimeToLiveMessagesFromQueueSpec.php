<?php

namespace Interop\Queue\Spec;

use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrQueue;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SendAndReceiveTimeToLiveMessagesFromQueueSpec extends TestCase
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
        $queue = $this->createQueue($context, 'send_and_receive_time_to_live_messages_from_queue_spec');

        $consumer = $context->createConsumer($queue);

        // guard
        $this->assertNull($consumer->receiveNoWait());

        $expectedBody = __CLASS__.time();

        $producer = $context->createProducer();

        $producer->setTimeToLive(2000);
        $producer->send($queue, $context->createMessage('it should not be received'));

        $producer->setTimeToLive(null);
        $producer->send($queue, $context->createMessage($expectedBody));

        sleep(3);

        $message = $consumer->receive(4000); // 8 sec

        $this->assertInstanceOf(PsrMessage::class, $message);
        $consumer->acknowledge($message);
        $this->assertSame($expectedBody, $message->getBody());
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

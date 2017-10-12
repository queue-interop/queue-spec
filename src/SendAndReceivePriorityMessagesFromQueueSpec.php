<?php

namespace Interop\Queue\Spec;

use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrQueue;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SendAndReceivePriorityMessagesFromQueueSpec extends TestCase
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
        $queue = $this->createQueue($context, 'send_and_receive_priority_messages_from_queue_spec');

        $consumer = $context->createConsumer($queue);

        // guard
        $this->assertNull($consumer->receiveNoWait());

        $expectedPriority5Body = __CLASS__.'_priority5_'.time();
        $expectedPriority1Body = __CLASS__.'_priority1_'.time();

        $producer = $context->createProducer();

        $producer->setPriority(1);
        $producer->send($queue, $context->createMessage($expectedPriority1Body));

        $producer->setPriority(5);
        $producer->send($queue, $context->createMessage($expectedPriority5Body));

        $message = $consumer->receive(8000); // 8 sec

        $this->assertInstanceOf(PsrMessage::class, $message);
        $consumer->acknowledge($message);
        $this->assertSame($expectedPriority5Body, $message->getBody());

        $message = $consumer->receive(8000); // 8 sec

        $this->assertInstanceOf(PsrMessage::class, $message);
        $consumer->acknowledge($message);
        $this->assertSame($expectedPriority1Body, $message->getBody());
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

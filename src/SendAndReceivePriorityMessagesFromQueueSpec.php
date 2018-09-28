<?php

namespace Interop\Queue\Spec;

use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Queue;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SendAndReceivePriorityMessagesFromQueueSpec extends TestCase
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
        $queue = $this->createQueue($context, 'send_and_receive_priority_messages_from_queue_spec');

        $consumer = $context->createConsumer($queue);

        // guard
        $this->assertNull($consumer->receiveNoWait());

        $expectedPriority5Body = __CLASS__.'_priority5_'.time();
        $expectedPriority1Body = __CLASS__.'_priority1_'.time();

        $producer = $context->createProducer();

        $producer->setPriority(1);
        $producer->send($queue, $this->createMessage($context, $expectedPriority1Body));

        $producer->setPriority(5);
        $producer->send($queue, $this->createMessage($context, $expectedPriority5Body));

        $message = $consumer->receive(8000); // 8 sec

        $this->assertInstanceOf(Message::class, $message);
        $consumer->acknowledge($message);
        $this->assertSame($expectedPriority5Body, $message->getBody());

        $message = $consumer->receive(8000); // 8 sec

        $this->assertInstanceOf(Message::class, $message);
        $consumer->acknowledge($message);
        $this->assertSame($expectedPriority1Body, $message->getBody());
    }

    /**
     * @return Context
     */
    abstract protected function createContext();

    /**
     * @param Context $context
     * @param string $body
     *
     * @return Message
     */
    protected function createMessage(Context $context, $body)
    {
        return $context->createMessage($body);
    }

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

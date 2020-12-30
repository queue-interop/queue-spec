<?php

namespace Interop\Queue\Spec;

use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Queue;
use Interop\Queue\Topic;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class RequeueMessageSpec extends TestCase
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
        $queue = $this->createQueue($context, 'requeue_message_spec');

        $consumer = $context->createConsumer($queue);

        // guard
        $this->assertNull($consumer->receiveNoWait());

        $expectedBody = __CLASS__.time();

        $context->createProducer()->send($queue, $context->createMessage($expectedBody));

        $message = $consumer->receive(2000);
        $this->assertInstanceOf(Message::class, $message);
        $consumer->reject($message, true);

        $requeuedMessage = $message = $consumer->receive(2000);
        $this->assertInstanceOf(Message::class, $requeuedMessage);
        $consumer->acknowledge($message);

        $this->assertSame($expectedBody, $requeuedMessage->getBody());
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

    /**
     * @param Context $context
     * @param string     $topicName
     *
     * @return Topic
     */
    protected function createTopic(Context $context, $topicName)
    {
        return $context->createTopic($topicName);
    }
}

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
abstract class SendToTopicAndReceiveNoWaitFromQueueSpec extends TestCase
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
        $topic = $this->createTopic($context, 'send_to_topic_and_receive_from_queue_spec');
        $queue = $this->createQueue($context, 'send_to_topic_and_receive_from_queue_spec');

        $consumer = $context->createConsumer($queue);

        // guard
        $this->assertNull($consumer->receiveNoWait());

        $expectedBody = __CLASS__.time();

        $context->createProducer()->send($topic, $context->createMessage($expectedBody));

        $startTime = microtime(true);
        $message = $consumer->receiveNoWait();

        $this->assertLessThan(2, microtime(true) - $startTime);

        $this->assertInstanceOf(Message::class, $message);
        $consumer->acknowledge($message);

        $this->assertSame($expectedBody, $message->getBody());
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

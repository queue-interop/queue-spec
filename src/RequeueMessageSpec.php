<?php

namespace Interop\Queue\Spec;

use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrQueue;
use Interop\Queue\PsrTopic;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class RequeueMessageSpec extends TestCase
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
        $queue = $this->createQueue($context, 'requeue_message_spec');

        $consumer = $context->createConsumer($queue);

        // guard
        $this->assertNull($consumer->receiveNoWait());

        $expectedBody = __CLASS__.time();

        $context->createProducer()->send($queue, $context->createMessage($expectedBody));

        $message = $consumer->receive(2000);
        $this->assertInstanceOf(PsrMessage::class, $message);
        $consumer->reject($message, true);

        $requeuedMessage = $message = $consumer->receive(2000);
        $this->assertInstanceOf(PsrMessage::class, $requeuedMessage);
        $consumer->acknowledge($message);

        $this->assertSame($expectedBody, $requeuedMessage->getBody());
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

    /**
     * @param PsrContext $context
     * @param string     $topicName
     *
     * @return PsrTopic
     */
    protected function createTopic(PsrContext $context, $topicName)
    {
        return $context->createTopic($topicName);
    }
}

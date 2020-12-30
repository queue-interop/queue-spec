<?php

namespace Interop\Queue\Spec;

use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Topic;
use PHPUnit\Framework\TestCase;

/**
 * @group functional
 */
abstract class SendToAndReceiveFromTopicSpec extends TestCase
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
        $topic = $this->createTopic($context, 'send_to_and_receive_from_topic_spec');

        $consumer = $context->createConsumer($topic);

        // guard
        $this->assertNull($consumer->receiveNoWait());

        $expectedBody = __CLASS__.time();

        $context->createProducer()->send($topic, $context->createMessage($expectedBody));

        $message = $consumer->receive(2000); // 2 sec

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
     * @param string     $topicName
     *
     * @return Topic
     */
    protected function createTopic(Context $context, $topicName)
    {
        return $context->createTopic($topicName);
    }
}

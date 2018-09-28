<?php

namespace Interop\Queue\Spec;

use Interop\Queue\Consumer;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Producer;
use Interop\Queue\Queue;
use Interop\Queue\Topic;
use PHPUnit\Framework\TestCase;

abstract class ContextSpec extends TestCase
{
    public function testShouldImplementContextInterface()
    {
        $this->assertInstanceOf(Context::class, $this->createContext());
    }

    public function testShouldCreateEmptyMessageOnCreateMessageMethodCallWithoutArguments()
    {
        $context = $this->createContext();

        $message = $context->createMessage();

        $this->assertInstanceOf(Message::class, $message);
        $this->assertSame('', $message->getBody());
        $this->assertSame([], $message->getHeaders());
        $this->assertSame([], $message->getProperties());
    }

    public function testShouldCreateMessageOnCreateMessageMethodCallWithArguments()
    {
        $context = $this->createContext();

        $message = $context->createMessage('theBody', ['foo' => 'fooVal'], ['bar' => 'barVal']);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertSame('theBody', $message->getBody());
        $this->assertSame(['bar' => 'barVal'], $message->getHeaders());
        $this->assertSame(['foo' => 'fooVal'], $message->getProperties());
    }

    public function testShouldCreateTopicWithGivenName()
    {
        $context = $this->createContext();

        $topic = $context->createTopic('theName');

        $this->assertInstanceOf(Topic::class, $topic);
        $this->assertSame('theName', $topic->getTopicName());
    }

    public function testShouldCreateQueueWithGivenName()
    {
        $context = $this->createContext();

        $queue = $context->createQueue('theName');

        $this->assertInstanceOf(Queue::class, $queue);
        $this->assertSame('theName', $queue->getQueueName());
    }

    public function testShouldCreateProducerOnCreateProducerMethodCall()
    {
        $context = $this->createContext();

        $producer = $context->createProducer();

        $this->assertInstanceOf(Producer::class, $producer);
    }

    public function testShouldCreateConsumerOnCreateConsumerMethodCall()
    {
        $context = $this->createContext();

        $consumer = $context->createConsumer($context->createQueue('aQueue'));

        $this->assertInstanceOf(Consumer::class, $consumer);
    }

    /**
     * @return Context
     */
    abstract protected function createContext();
}

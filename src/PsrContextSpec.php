<?php

namespace Interop\Queue\Spec;

use Interop\Queue\PsrConsumer;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProducer;
use Interop\Queue\PsrQueue;
use Interop\Queue\PsrTopic;
use PHPUnit\Framework\TestCase;

abstract class PsrContextSpec extends TestCase
{
    public function testShouldImplementPsrContextInterface()
    {
        $this->assertInstanceOf(PsrContext::class, $this->createContext());
    }

    public function testShouldCreateEmptyMessageOnCreateMessageMethodCallWithoutArguments()
    {
        $context = $this->createContext();

        $message = $context->createMessage();

        $this->assertInstanceOf(PsrMessage::class, $message);
        $this->assertSame('', $message->getBody());
        $this->assertSame([], $message->getHeaders());
        $this->assertSame([], $message->getProperties());
    }

    public function testShouldCreateMessageOnCreateMessageMethodCallWithArguments()
    {
        $context = $this->createContext();

        $message = $context->createMessage('theBody', ['foo' => 'fooVal'], ['bar' => 'barVal']);

        $this->assertInstanceOf(PsrMessage::class, $message);
        $this->assertSame('theBody', $message->getBody());
        $this->assertSame(['bar' => 'barVal'], $message->getHeaders());
        $this->assertSame(['foo' => 'fooVal'], $message->getProperties());
    }

    public function testShouldCreateTopicWithGivenName()
    {
        $context = $this->createContext();

        $topic = $context->createTopic('theName');

        $this->assertInstanceOf(PsrTopic::class, $topic);
        $this->assertSame('theName', $topic->getTopicName());
    }

    public function testShouldCreateQueueWithGivenName()
    {
        $context = $this->createContext();

        $queue = $context->createQueue('theName');

        $this->assertInstanceOf(PsrQueue::class, $queue);
        $this->assertSame('theName', $queue->getQueueName());
    }

    public function testShouldCreateProducerOnCreateProducerMethodCall()
    {
        $context = $this->createContext();

        $producer = $context->createProducer();

        $this->assertInstanceOf(PsrProducer::class, $producer);
    }

    public function testShouldCreateConsumerOnCreateConsumerMethodCall()
    {
        $context = $this->createContext();

        $consumer = $context->createConsumer($context->createQueue('aQueue'));

        $this->assertInstanceOf(PsrConsumer::class, $consumer);
    }

    /**
     * @return PsrContext
     */
    abstract protected function createContext();
}

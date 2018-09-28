<?php

namespace Interop\Queue\Spec;

use Interop\Queue\Topic;
use PHPUnit\Framework\TestCase;

abstract class TopicSpec extends TestCase
{
    const EXPECTED_TOPIC_NAME = 'theTopicName';

    public function testShouldImplementTopicInterface()
    {
        $this->assertInstanceOf(Topic::class, $this->createTopic());
    }

    public function testShouldReturnTopicName()
    {
        $topic = $this->createTopic();

        $this->assertSame(self::EXPECTED_TOPIC_NAME, $topic->getTopicName());
    }

    /**
     * @return Topic
     */
    abstract protected function createTopic();
}

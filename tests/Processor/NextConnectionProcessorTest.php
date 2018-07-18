<?php

namespace Phlow\Tests\Engine;

use Phlow\Node\Fake;
use Phlow\Engine\Exchange;
use Phlow\Processor\NextConnectionProcessor;
use Phlow\Processor\UnmatchedConditionException;
use Phlow\Connection\Connection;
use PHPUnit\Framework\TestCase;

class NextConnectionProcessorTest extends TestCase
{
    public function testNextConnection()
    {
        $node = new Fake();
        $nextNode = new Fake();
        $anotherNode = new Fake();
        $connection1 = new Connection($node, $nextNode, Connection::LABEL_NEXT);
        $connection2 = new Connection($node, $anotherNode, Connection::LABEL_NEXT);

        $processor = new NextConnectionProcessor();
        $actualConnection = $processor->process($node, new Exchange());
        $this->assertEquals($connection1, $actualConnection);
    }

    public function testParentConnection()
    {
        $node = new Fake();
        $parentNode = new Fake();
        $nextNode = new Fake();
        $parentNextNode = new Fake();
        $connection1 = new Connection($node, $parentNode, Connection::LABEL_PARENT);
        $connection2 = new Connection($node, $nextNode, Connection::LABEL_NEXT);
        $connection3 = new Connection($parentNode, $parentNextNode, Connection::LABEL_NEXT);

        $processor = new NextConnectionProcessor();
        $actualConnection = $processor->process($node, new Exchange());
        $this->assertEquals($connection3, $actualConnection);
    }

    public function testNoConnection()
    {
        $processor = new NextConnectionProcessor();
        $this->expectException(UnmatchedConditionException::class);
        $processor->process(new Fake(), new Exchange());
    }
}

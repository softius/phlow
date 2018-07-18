<?php

namespace Phlow\Tests\Node;

use Phlow\Connection\Connection;
use Phlow\Node\Fake;
use PHPUnit\Framework\TestCase;

class AbstractNodeTest extends TestCase
{

    public function testIncomingConnections()
    {
        $node = new Fake();
        $this->assertFalse($node->hasIncomingConnections());
        $this->assertFalse($node->hasIncomingConnections(Connection::LABEL_NEXT));
        $this->assertFalse($node->hasIncomingConnections(Connection::LABEL_CHILD));

        $connection = new Connection(new Fake(), $node, Connection::LABEL_NEXT);
        $this->assertTrue($node->hasIncomingConnections());
        $this->assertTrue($node->hasIncomingConnections(Connection::LABEL_NEXT));
        $this->assertFalse($node->hasIncomingConnections(Connection::LABEL_CHILD));
        $this->assertEquals([$connection], $node->getIncomingConnections());
        $this->assertEquals([$connection], $node->getIncomingConnections(Connection::LABEL_NEXT));
    }

    public function testOutgoingConnections()
    {
        $node = new Fake();
        $this->assertFalse($node->hasOutgoingConnections());
        $this->assertFalse($node->hasOutgoingConnections(Connection::LABEL_NEXT));
        $this->assertFalse($node->hasOutgoingConnections(Connection::LABEL_CHILD));

        $connection = new Connection($node, new Fake(), Connection::LABEL_NEXT);
        $this->assertTrue($node->hasOutgoingConnections());
        $this->assertTrue($node->hasOutgoingConnections(Connection::LABEL_NEXT));
        $this->assertFalse($node->hasOutgoingConnections(Connection::LABEL_CHILD));
        $this->assertEquals([$connection], $node->getOutgoingConnections());
        $this->assertEquals([$connection], $node->getOutgoingConnections(Connection::LABEL_NEXT));
    }
}

<?php

namespace Phlow\Tests\Connection;

use Phlow\Connection\Connection;
use Phlow\Expression\Simple;
use Phlow\Node\Fake;
use Phlow\Tests\Expression\TestExpression;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    public function testGetSource()
    {
        $source = new Fake();
        $target = new Fake();
        $connection = new Connection($source, $target, Connection::LABEL_NEXT);
        $this->assertEquals($source, $connection->getSource());
    }

    public function testIsConditional()
    {
        $source = new Fake();
        $target = new Fake();
        $expression = new TestExpression();
        $connection = new Connection($source, $target, Connection::LABEL_NEXT, $expression);
        $this->assertTrue($connection->isConditional());
    }

    public function testGetTarget()
    {
        $source = new Fake();
        $target = new Fake();
        $connection = new Connection($source, $target, Connection::LABEL_NEXT);
        $this->assertEquals($target, $connection->getTarget());
    }

    public function testHasLabel()
    {
        $source = new Fake();
        $target = new Fake();
        $connection = new Connection($source, $target, Connection::LABEL_NEXT);
        $this->assertTrue($connection->hasLabel(Connection::LABEL_NEXT));
    }

    public function testGetCondition()
    {
        $source = new Fake();
        $target = new Fake();
        $expression = new TestExpression();
        $connection = new Connection($source, $target, Connection::LABEL_NEXT, $expression);
        $this->assertEquals($expression, $connection->getCondition());
    }

    public function testToString()
    {
        $this->assertEquals('Fake', (string) (new Fake()));
    }
}

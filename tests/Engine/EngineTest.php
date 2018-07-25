<?php

namespace Phlow\Tests\Engine;

use Phlow\Engine\Engine;
use Phlow\Model\Workflow;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{

    public function testCreateInstance()
    {
        $workflow = new Workflow('test');
        $engine = new Engine();
        $engine->add($workflow);

        $instance = $engine->createInstance('test', []);
        $this->assertEquals($workflow, $instance->getWorkflow());
    }

    public function testAdd()
    {
        $workflow = new Workflow('test');
        $engine = new Engine();
        $engine->add($workflow);
        $this->assertEquals($workflow, $engine->get('test'));

        $this->expectException(\InvalidArgumentException::class);
        $engine->add($workflow);
    }

    public function testGet()
    {
        $engine = new Engine();
        $this->expectException(\OutOfBoundsException::class);
        $engine->get('-');
    }
}

<?php

namespace Phlow\Tests\Model;

use Phlow\Engine\Engine;
use Phlow\Node\Callback;
use Phlow\Node\End;
use Phlow\Node\Start;
use Phlow\Model\Workflow;
use Phlow\Model\WorkflowBuilder;
use Phlow\Engine\WorkflowInstance;
use Phlow\Engine\InvalidStateException;
use Phlow\Connection\Connection;
use Phlow\Tests\Engine\TestLogger;
use PHPUnit\Framework\TestCase;

class WorkflowInstanceTest extends TestCase
{
    private $engine;

    public function setUp()
    {
        $this->engine = new Engine();
    }

    public function testAdvance()
    {
        $workflow = $this->getPipeline();
        $workflow->advance(1);
        $this->assertTrue($workflow->current() instanceof Start);
        $this->assertTrue($workflow->next() instanceof Callback);
        $this->assertTrue($workflow->inProgress());
        $this->assertFalse($workflow->isCompleted());
    }

    public function testExchangeInOut()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->start()
            ->callback(function ($in) {
                $in->num = 10;
                return $in;
            })
            ->callback(function ($in) {
                $in->num = 20;
                return $in;
            })
            ->end();
        $instance = new WorkflowInstance($this->engine, $builder->getWorkflow(), (object) ['num' => 0]);
        $d = $instance->advance(2);
        $this->assertEquals(10, $d->num);
    }

    public function testNoStartEvent()
    {
        $flow = new Workflow();
        $instance = new WorkflowInstance($this->engine, $flow, []);
        $this->expectException(InvalidStateException::class);
        $instance->advance();
    }

    public function testAlreadyCompleted()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->start()
            ->end();
        $instance = new WorkflowInstance($this->engine, $builder->getWorkflow(), []);
        $this->expectException(InvalidStateException::class);
        $instance->advance(3);

        $this->assertTrue($instance->isCompleted());
        $this->assertFalse($instance->inProgress());
        $this->assertTrue($instance->current() instanceof End);
    }

    public function testCurrentBeforeExecution()
    {
        $workflow = $this->getPipeline();
        $this->expectException(InvalidStateException::class);
        $workflow->current();
    }

    public function testErrorHandling()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->catchAll()
            ->callback(function ($d) {
                $d['num']++;
                return $d;
            })
            ->end();

        $builder
            ->start()
            ->callback(function () {
                throw new \RuntimeException();
            })
            ->end();
        $instance = new WorkflowInstance($this->engine, $builder->getWorkflow(), ['num' => 10]);

        $instance->advance(2);
        $this->assertNotInstanceOf(End::class, $instance->current());
    }

    public function testMissingErrorHandling()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->start()
            ->callback(function () {
                throw new \BadFunctionCallException();
            })
            ->end();
        $instance = new WorkflowInstance($this->engine, $builder->getWorkflow(), ['num' => 10]);

        $this->expectException(\BadFunctionCallException::class);
        $instance->advance(2);
    }

    public function testUndefinedErrorHandling()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->catch(\OutOfBoundsException::class)
            ->callback(function ($d) {
                $d['num']++;
                return $d;
            })
            ->end();
        $builder
            ->start()
            ->callback(function () {
                throw new \BadFunctionCallException();
            })
            ->end();
        $instance = new WorkflowInstance($this->engine, $builder->getWorkflow(), ['num' => 10]);

        $this->expectException(\BadFunctionCallException::class);
        $instance->advance(2);
    }

    public function testExecution()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->start()
            ->callback(function ($d) {
                return $d;
            })
            ->end();
        $instance = new WorkflowInstance($this->engine, $builder->getWorkflow(), ['num' => 10]);

        $instance->execute();
        $this->assertTrue($instance->isCompleted());
    }

    public function testLogger()
    {
        $logger = new TestLogger();
        $instance = $this->getPipeline();
        $instance->setLogger($logger);
        $instance->execute();

        // 1. Workflow execution initiated
        // 2/4/6 Start/Callback/Callback reached
        // 3/5/7. Start/Callback/Callback executed
        // 8. Workflow execution reached Phlow\Node\End
        // 9. Workflow execution completed
        $this->assertEquals(9, count($logger->getAllRecords()));
    }

    public function testExecutionPath()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->start()
            ->end();
        $instance = new WorkflowInstance($this->engine, $builder->getWorkflow(), []);
        $instance->execute();

        $this->assertEquals(3, count($instance->getExecutionPath()));
        $path = iterator_to_array($instance->getExecutionPath());
        $this->assertInstanceOf(Start::class, $path[0]);
        $this->assertInstanceOf(Connection::class, $path[1]);
        $this->assertInstanceOf(End::class, $path[2]);
    }

    public function testGetWorkflow()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->start()
            ->end();
        $instance = new WorkflowInstance($this->engine, $builder->getWorkflow(), []);
        $this->assertEquals($builder->getWorkflow(), $instance->getWorkflow());
    }

    private function getPipeline()
    {
        $getInput = function ($d) {
            $d['a'] = 1;
            $d['b'] = 2;
            return $d;
        };

        $sum = function ($d) {
            $d['c'] = $d['a'] + $d['b'];
            return $d;
        };

        $error = function ($e) {
            throw $e;
        };

        $builder = new WorkflowBuilder();
        $builder
            ->catchAll()
            ->callback($error)
            ->end();

        $builder
            ->start()
            ->callback($getInput)
            ->callback($sum)
            ->end();

        $in = ['a' => null, 'b' => null, 'c' => null];
        return new WorkflowInstance($this->engine, $builder->getWorkflow(), $in);
    }
}

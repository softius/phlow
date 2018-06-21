<?php

namespace Phlow\Tests\Workflow;

use Phlow\Activity\Task;
use Phlow\Model\Workflow\Workflow;
use Phlow\Model\Workflow\WorkflowBuilder;
use Phlow\Engine\WorkflowInstance;
use PHPUnit\Framework\TestCase;

class WorkflowInstanceTest extends TestCase
{
    public function testAdvance()
    {
        $workflow = $this->getPipeline();
        $workflow->advance(1);
        $this->assertTrue($workflow->current() instanceof Task);
    }

    public function testNoStartEvent()
    {
        $flow = new Workflow();
        $instance = new WorkflowInstance($flow, []);
        $this->expectException(\RuntimeException::class);
        $instance->advance();
    }

    public function testAlreadyCompleted()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->start('start', 'end')
            ->end('end');
        $instance = new WorkflowInstance($builder->getWorkflow(), []);
        $this->expectException(\RuntimeException::class);
        $instance->advance(3);
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
        $builder->catch($error);
        $builder
            ->start('start', 'getInput')
            ->script('getInput', 'sum', 'error')
                ->callback($getInput)
            ->script('sum', 'end', 'error')
                ->callback($sum)
            ->end('end')
            ->error('error', 'end');

        $in = ['a' => null, 'b' => null, 'c' => null];
        return new WorkflowInstance($builder->getWorkflow(), $in);
    }
    /*
    public function testConditionalFlow()
    {
        $helloName  = function ($d) {
            $d->message = sprintf("Hello %s!", $d->name);
            return $d;
        };
        $helloWorld  = function ($d) {
            $d->message = 'Hello world';
            return $d;
        };

        $builder = new WorkflowBuilder();
        $builder
            ->start('start', 'nameIsProvided')
            ->choice('nameIsProvided')
            ->when('name == null', 'helloWorld')
            ->when('true', 'hello')
            ->script('helloWorld', 'end', 'end')
                ->callback($helloWorld)
            ->script('hello', 'end', 'end')
                ->callback($helloName)
            ->end('end');

        $d = (object) ['name' => 'phlow', 'message' => null];
        $instance = new WorkflowInstance($builder->getWorkflow(), $d);
        $r = $instance->advance(2);
        $this->assertEquals("Hello phlow!", $r->message);
    }
    */
}

<?php

namespace Phlow\Tests\Workflow;

use Phlow\Event\EndEvent;
use Phlow\Event\ErrorEvent;
use Phlow\Workflow\Workflow;
use Phlow\Workflow\WorkflowBuilder;
use Phlow\Workflow\WorkflowInstance;

class WorkflowInstanceTest extends \PHPUnit\Framework\TestCase
{
    public function testPipelineHappyPath()
    {
        $instance = $this->getPipeline();
        $out = $instance->advance(3);
        $this->assertEquals(true, $instance->isCompleted());
        $this->assertEquals(3, $out['c']);
    }

    public function testPipelineError()
    {
        $obj = (object) ['invoked' => false];
        $builder = new WorkflowBuilder();
        $builder
            ->start('start', 'error')
            ->error('error', 'script')
            ->script('script', function ($e) use ($obj) {
                $obj->invoked = true;
            }, 'end', 'end')
            ->end('end');

        $instance = new WorkflowInstance($builder->getWorkflow(), []);
        $instance->advance(2);
        $this->assertEquals(true, $obj->invoked);
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
            ->script('getInput', $getInput, 'sum', 'error')
            ->script('sum', $sum, 'end', 'error')
            ->end('end')
            ->error('error', 'end');

        $in = ['a' => null, 'b' => null, 'c' => null];
        return new WorkflowInstance($builder->getWorkflow(), $in);
    }

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
            ->when(function ($d) {
                return empty($d);
            }, 'helloWorld')
            ->when(function ($d) {
                return !empty($d);
            }, 'hello')
            ->script('helloWorld', $helloWorld, 'end', 'end')
            ->script('hello', $helloName, 'end', 'end')
            ->end('end');

        $d = (object) ['name' => 'phlow', 'message' => null];
        $instance = new WorkflowInstance($builder->getWorkflow(), $d);
        $r = $instance->advance(2);
        $this->assertEquals("Hello phlow!", $r->message);
    }
}

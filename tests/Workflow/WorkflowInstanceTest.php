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
        $builder->start(
            $builder->error(function ($e) use ($obj) {
                $obj->invoked = true;
            })
        );

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
        $builder->start($builder->end());
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
        $builder->start(
            $builder->task(
                $getInput,
                $builder->task($sum, $builder->end())
            )
        );

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
        $builder->start(
            $builder->exclusive()
                ->when(
                    function ($d) {
                        return empty($d);
                    },
                    $builder->task(
                        $helloWorld,
                        $builder->end()
                    )
                )
                ->when(
                    function ($d) {
                        return !empty($d);
                    },
                    $builder->task(
                        $helloName,
                        $builder->end()
                    )
                )
        );

        $d = (object) ['name' => 'phlow', 'message' => null];
        $instance = new WorkflowInstance($builder->getWorkflow(), $d);
        $r = $instance->advance(2);
        $this->assertEquals("Hello phlow!", $r->message);
    }
}

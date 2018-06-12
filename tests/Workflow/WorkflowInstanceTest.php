<?php

namespace Phlow\Tests\Workflow;

use Phlow\Event\EndEvent;
use Phlow\Event\ErrorEvent;
use Phlow\Workflow\Workflow;
use Phlow\Workflow\WorkflowInstance;

class WorkflowTest extends \PHPUnit\Framework\TestCase
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
        $flow = new Workflow();
        $flow->start(
            $flow->error(function ($e) use ($obj) {
                $obj->invoked = true;
            })
        );

        $instance = new WorkflowInstance($flow, []);
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
        $flow = new Workflow();
        $flow->start($flow->end());
        $instance = new WorkflowInstance($flow, []);
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

        $flow = new Workflow();
        $flow->catch($error);
        $flow->start(
            $flow->task(
                $getInput,
                $flow->task($sum, $flow->end())
            )
        );

        $in = ['a' => null, 'b' => null, 'c' => null];
        return new WorkflowInstance($flow, $in);
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

        $flow = new Workflow();
        $flow->start(
            $flow->exclusive()
                ->when(
                    function ($d) {
                        return empty($d);
                    },
                    $flow->task(
                        $helloWorld,
                        $flow->end()
                    )
                )
                ->when(
                    function ($d) {
                        return !empty($d);
                    },
                    $flow->task(
                        $helloName,
                        $flow->end()
                    )
                )
        );

        $d = (object) ['name' => 'phlow', 'message' => null];
        $instance = new WorkflowInstance($flow, $d);
        $r = $instance->advance(2);
        $this->assertEquals("Hello phlow!", $r->message);
    }
}

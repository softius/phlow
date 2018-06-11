<?php

namespace Phlow\Tests\Workflow;

use Phlow\Event\EndEvent;
use Phlow\Event\ErrorEvent;
use Phlow\Workflow\Workflow;

class WorkflowTest extends \PHPUnit\Framework\TestCase
{
    public function testPipelineHappyPath()
    {
        $flow = $this->getPipeline();
        $out = $flow->advance(3);
        $this->assertEquals(true, $flow->isCompleted());
        $this->assertEquals(3, $out['c']);
    }

    public function testPipelineError()
    {
        $obj = (object) ['invoked' => false];
        $flow = new Workflow([]);
        $flow->start(
            $flow->error(function ($e) use ($obj) {
                $obj->invoked = true;
            })
        );

        $flow->advance(2);
        $this->assertEquals(true, $obj->invoked);
    }

    public function testNoStartEvent()
    {
        $flow = new Workflow([]);
        $this->expectException(\RuntimeException::class);
        $flow->advance();
    }

    public function testAlreadyCompleted()
    {
        $flow = new Workflow([]);
        $flow->start($flow->end());
        $this->expectException(\RuntimeException::class);
        $flow->advance(3);
    }

    private function getPipeline()
    {
        $in = ['a' => null, 'b' => null, 'c' => null];

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

        $flow = new Workflow($in);
        $flow->catch($error);
        $flow->start(
            $flow->task(
                $getInput,
                $flow->task($sum, $flow->end())
            )
        );

        return $flow;
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

        $d = (object) ['name' => 'phlow', 'message' => null];
        $flow = new Workflow($d);
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

        $r = $flow->advance(2);
        $this->assertEquals("Hello phlow!", $r->message);
    }
}

<?php

namespace Phlow\Tests\Workflow;

use Phlow\Activity\Task;
use Phlow\Event\EndEvent;
use Phlow\Event\StartEvent;
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
        $this->assertTrue($workflow->inProgress());
        $this->assertFalse($workflow->isCompleted());
    }

    public function testExchangeInOut()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->start('start', 'script1')
            ->script('script1', 'script2', 'end')
                ->callback(function ($in) {
                    $in->num = 10;
                    return $in;
                })
            ->script('script2', 'end', 'end')
                ->callback(function ($in) {
                    $in->num = 20;
                    return $in;
                })
            ->end('end');
        $instance = new WorkflowInstance($builder->getWorkflow(), (object) ['num' => 0]);
        $d = $instance->advance(2);
        $this->assertEquals(10, $d->num);
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

        $this->assertTrue($instance->isCompleted());
        $this->assertFalse($instance->inProgress());
        $this->assertTrue($instance->current() instanceof EndEvent);
    }

    public function testCurrentBeforeExecution()
    {
        $workflow = $this->getPipeline();
        $this->expectException(\RuntimeException::class);
        $workflow->current();
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
}

<?php

namespace Phlow\Tests\Workflow;

use Phlow\Activity\Task;
use Phlow\Event\EndEvent;
use Phlow\Model\Workflow;
use Phlow\Model\WorkflowBuilder;
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

    public function testErrorHandling()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->catchAll('errorTask')
            ->script('errorTask', 'errorEnd', 'errorEnd')
            ->callback(function ($d) {
                $d['num']++;
                return $d;
            })
            ->end('errorEnd');

        $builder
            ->start('start', 'task')
            ->script('task', 'end', 'end')
            ->callback(function () {
                throw new \Exception();
            })
            ->end('end');
        $instance = new WorkflowInstance($builder->getWorkflow(), ['num' => 10]);

        $instance->advance(2);
        $this->assertNotInstanceOf(EndEvent::class, $instance->current());
    }

    public function testMissingErrorHandling()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->start('start', 'task')
            ->script('task', 'end', 'end')
            ->callback(function () {
                throw new \Exception();
            })
            ->end('end');
        $instance = new WorkflowInstance($builder->getWorkflow(), ['num' => 10]);

        $this->expectException(\RuntimeException::class);
        $instance->advance(2);
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
            ->catchAll('errorTask')
            ->script('errorTask', 'errorEnd', 'errorEnd')
            ->callback($error)
            ->end('errorEnd');

        $builder
            ->start('start', 'getInput')
            ->script('getInput', 'sum', 'end')
                ->callback($getInput)
            ->script('sum', 'end', 'end')
                ->callback($sum)
            ->end('end');

        $in = ['a' => null, 'b' => null, 'c' => null];
        return new WorkflowInstance($builder->getWorkflow(), $in);
    }
}

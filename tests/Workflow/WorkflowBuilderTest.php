<?php

namespace Phlow\Tests\Workflow;

use Phlow\Activity\Task;
use Phlow\Event\StartEvent;
use Phlow\Event\EndEvent;
use Phlow\Gateway\ExclusiveGateway;
use Phlow\Model\WorkflowBuilder;
use PHPUnit\Framework\TestCase;

class WorkflowBuilderTest extends TestCase
{
    public function testStartEnd()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->start("start", "end")
            ->end("end");

        $workflow = $builder->getWorkflow();
        $this->assertTrue($workflow->get('start') instanceof StartEvent);
        $this->assertTrue($workflow->get('end') instanceof EndEvent);
    }

    public function testTask()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->end("end")
            ->script("script", "end", "end")
            ->callback(function ($d) {
                return $d;
            });

        $workflow = $builder->getWorkflow();
        $this->assertTrue($workflow->get('script') instanceof Task);
        $this->assertTrue($workflow->get('script')->hasCallback());
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
            ->when('name == null', 'helloWorld')
            ->when('true', 'hello')
            ->script('helloWorld', 'end', 'end')
            ->callback($helloWorld)
            ->script('hello', 'end', 'end')
            ->callback($helloName)
            ->end('end');

        $workflow = $builder->getWorkflow();
        $this->assertTrue($workflow->get('nameIsProvided') instanceof ExclusiveGateway);
        $this->assertEquals(2, count($workflow->get('nameIsProvided')->getOutgoingconnections()));
    }
}

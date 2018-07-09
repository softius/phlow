<?php

namespace Phlow\Tests\Workflow;

use Phlow\Activity\Task;
use Phlow\Gateway\ExclusiveGateway;
use Phlow\Model\WorkflowBuilder;
use PHPUnit\Framework\TestCase;

class WorkflowBuilderTest extends TestCase
{
    public function testTask()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->end()
            ->script(function ($d) {
                return $d;
            });

        $workflow = $builder->getWorkflow();

        /** @var Task $node */
        $task = $workflow->getAllByClass(Task::class)[0];
        $this->assertTrue($task instanceof Task);
        $this->assertTrue($task->hasCallback());
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
            ->start()
            ->choice()
            ->when('name == null')
                ->script()
                    ->callback($helloWorld)
            ->otherwise()
                ->script()
                    ->callback($helloName)
            ->end();

        $workflow = $builder->getWorkflow();

        /** @var ExclusiveGateway $node */
        $node = $workflow->getAllByClass(ExclusiveGateway::class)[0];
        $this->assertTrue($node instanceof ExclusiveGateway);
        $this->assertEquals(2, count($node->getOutgoingconnections()));
    }

    public function testNestedConditionalFlows()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->start()
            ->choice()
            ->when('name == null')
                ->choice()
                    ->when('isDay')
                        ->script()
                    ->when('isNight')
                        ->script()
                    ->otherwise()
                        ->script()
                ->endChoice()
            ->otherwise()
                ->script()
            ->end();

        $workflow = $builder->getWorkflow();

        /** @var ExclusiveGateway $node */
        $node = $workflow->getAllByClass(ExclusiveGateway::class)[0];
        $this->assertTrue($node instanceof ExclusiveGateway);
        $this->assertEquals(2, count($node->getOutgoingconnections()));
    }
}

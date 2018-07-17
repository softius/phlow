<?php

namespace Phlow\Tests\Workflow;

use Phlow\Activity\Task;
use Phlow\Gateway\ExclusiveGateway;
use Phlow\Model\WorkflowBuilder;
use Phlow\Model\WorkflowConnection;
use PHPUnit\Framework\TestCase;

class WorkflowBuilderTest extends TestCase
{
    public function testTask()
    {
        $builder = new WorkflowBuilder();
        $builder
            ->end()
            ->callback(function ($d) {
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
                ->callback($helloWorld)
            ->otherwise()
                ->callback($helloName)
            ->end();

        $workflow = $builder->getWorkflow();

        /** @var ExclusiveGateway $node */
        $node = $workflow->getAllByClass(ExclusiveGateway::class)[0];
        $this->assertTrue($node instanceof ExclusiveGateway);
        $this->assertEquals(3, count($node->getOutgoingconnections()));
        $this->assertEquals(2, count($node->getOutgoingconnections(WorkflowConnection::LABEL_CHILD)));
        $this->assertEquals(1, count($node->getOutgoingconnections(WorkflowConnection::LABEL_NEXT)));
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
                        ->callback()
                    ->when('isNight')
                        ->callback()
                    ->otherwise()
                        ->callback()
                ->end()
            ->otherwise()
                ->callback()
            ->endAll();

        $workflow = $builder->getWorkflow();

        /** @var ExclusiveGateway $node */
        $node = $workflow->getAllByClass(ExclusiveGateway::class)[1];
        $this->assertTrue($node instanceof ExclusiveGateway);
        $this->assertEquals(4, count($node->getOutgoingconnections()));
        $this->assertEquals(3, count($node->getOutgoingconnections(WorkflowConnection::LABEL_CHILD)));
        $this->assertEquals(1, count($node->getOutgoingconnections(WorkflowConnection::LABEL_NEXT)));
    }
}

<?php

namespace Phlow\Tests\Workflow;

use Phlow\Node\Callback;
use Phlow\Node\Choice;
use Phlow\Model\WorkflowBuilder;
use Phlow\Connection\Connection;
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

        /** @var Callback $node */
        $task = $workflow->getAllByClass(Callback::class)[0];
        $this->assertTrue($task instanceof Callback);
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

        /** @var Choice $node */
        $node = $workflow->getAllByClass(Choice::class)[0];
        $this->assertTrue($node instanceof Choice);
        $this->assertEquals(3, count($node->getOutgoingconnections()));
        $this->assertEquals(2, count($node->getOutgoingconnections(Connection::LABEL_CHILD)));
        $this->assertEquals(1, count($node->getOutgoingconnections(Connection::LABEL_NEXT)));
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

        /** @var Choice $node */
        $node = $workflow->getAllByClass(Choice::class)[1];
        $this->assertTrue($node instanceof Choice);
        $this->assertEquals(4, count($node->getOutgoingconnections()));
        $this->assertEquals(3, count($node->getOutgoingconnections(Connection::LABEL_CHILD)));
        $this->assertEquals(1, count($node->getOutgoingconnections(Connection::LABEL_NEXT)));
    }
}

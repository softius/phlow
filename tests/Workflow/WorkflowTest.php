<?php

namespace Phlow\Tests\Workflow;

use Phlow\Event\EndEvent;
use Phlow\Event\StartEvent;
use Phlow\Model\Workflow\NotFoundException;
use Phlow\Model\Workflow\Workflow;

class WorkflowTest extends \PHPUnit\Framework\TestCase
{
    public function testAddGetFound()
    {
        $workflow = new Workflow();
        $startEvent = new StartEvent();
        $workflow->add($startEvent, 'start');
        $this->assertEquals($startEvent, $workflow->get('start'));
    }

    public function testDuplicates()
    {
        $workflow = new Workflow();
        $workflow->add(new StartEvent(), 'start');
        $this->expectException(\RuntimeException::class);
        $workflow->add(new StartEvent(), 'start');
    }

    public function testGetNotFound()
    {
        $workflow = $this->getWorkflow();
        $this->expectException(NotFoundException::class);
        $this->assertEquals(null, $workflow->get('id-does-not-exist'));
    }

    public function testGetAll()
    {
        $workflow = $this->getWorkflow();
        $this->assertEquals(2, count($workflow->getAll()));
        $this->assertEquals(1, count($workflow->getAllByClass(StartEvent::class)));
    }

    public function testRemove()
    {
        $workflow = new Workflow();
        $end = new EndEvent();
        $start = new StartEvent();
        $workflow->addAll($start, $end);

        $this->expectException(NotFoundException::class);
        $workflow->remove(new EndEvent());
        $this->assertEquals(2, count($workflow->getAll()));

        $workflow->remove($end);
        $this->assertEquals(1, count($workflow->getAll()));
    }

    private function getWorkflow()
    {
        $workflow = new Workflow();
        $end = new EndEvent();
        $start = new StartEvent($end);

        $workflow->addAll($start, $end);

        return $workflow;
    }
}

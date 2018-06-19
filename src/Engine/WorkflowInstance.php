<?php

namespace Phlow\Engine;

use Phlow\Event\EndEvent;
use Phlow\Event\StartEvent;
use Phlow\Model\Workflow\Workflow;
use Phlow\Model\Workflow\WorkflowNode;

/**
 * Class WorkflowInstance
 * Represents an instance of the provided workflow.
 * @package Phlow\Workflow
 */
class WorkflowInstance
{
    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var Exchange Last exchange between workflow nodes
     */
    private $exchange;

    /**
     * @var WorkflowNode Last executed node
     */
    private $currentNode;

    /**
     * WorkflowInstance constructor.
     * @param Workflow $workflow
     * @param $inbound
     */
    public function __construct(Workflow $workflow, $inbound)
    {
        $this->workflow = $workflow;
        $this->exchange = new Exchange($inbound);
        $this->currentNode = null;
    }

    /**
     * Proceeds to the next workflow node and executes it
     * @param int $howMany
     * @return object
     */
    public function advance($howMany = 1)
    {
        if ($this->isCompleted()) {
            throw new \RuntimeException("Workflow has been already completed.");
        }

        // Retrieve and execute the next node
        $node = $this->next();
        if ($node instanceof ExecutableNode) {
            $this->exchange->setOut(
                $node->execute($this->exchange->in())
            );

            // Prepare an exchange for the next node
            $this->exchange = new Exchange($this->exchange->out());
        }

        $this->currentNode = $node;
        return $howMany === 1 ? $this->exchange->in() : $this->advance($howMany - 1);
    }

    /**
     * Finds and return the next node to be executed
     * @return WorkflowNode
     */
    private function next(): WorkflowNode
    {
        $startEvents = $this->workflow->getAllByClass(StartEvent::class);
        if ($this->currentNode === null && empty($startEvents)) {
            throw new \RuntimeException('Start event is missing');
        }

        $this->currentNode = $this->currentNode ?? $startEvents[0];

        return $this->currentNode->next(
            $this->exchange->in()
        );
    }

    /**
     * Returns true only and only if the execution has reached and End event.
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->currentNode instanceof EndEvent;
    }

    /**
     * Returns the last executed node.
     * @return null|WorkflowNode
     */
    public function current(): WorkflowNode
    {
        return $this->currentNode;
    }
}

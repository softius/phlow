<?php

namespace Phlow\Model;

class WorkflowConnection implements WorkflowObject
{
    private $source;
    private $target;
    private $condition;

    public function __construct(WorkflowNode $source, WorkflowNode $target, $condition = true)
    {
        $source->addOutgoingConnection($this);
        $target->addIncomingConnection($this);

        $this->source = $source;
        $this->target = $target;
        $this->condition = $condition;
    }

    /**
     * @return WorkflowNode
     */
    public function getSource(): WorkflowNode
    {
        return $this->source;
    }

    /**
     * @return WorkflowNode
     */
    public function getTarget(): WorkflowNode
    {
        return $this->target;
    }

    public function getCondition()
    {
        return $this->condition;
    }

    public function isConditional()
    {
        return !empty($this->condition);
    }
}

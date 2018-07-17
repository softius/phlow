<?php

namespace Phlow\Model;

class WorkflowConnection implements WorkflowObject
{
    use RenderableObject {
        __toString as private getClassString;
    }

    private $source;
    private $target;
    private $condition;
    private $label;

    public const LABEL_OPEN = 1;
    public const LABEL_CLOSED = 2;
    public const LABEL_NEXT = 3;

    public function __construct(WorkflowNode $source, WorkflowNode $target, int $label, $condition = true)
    {
        $source->addOutgoingConnection($this);
        $target->addIncomingConnection($this);

        $this->source = $source;
        $this->target = $target;
        $this->label = $label;
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
     *
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

    public function __toString()
    {
        $default = $this->getClassString();
        return ($this->isConditional()) ? sprintf("%s (%s)", $default, $this->getCondition()) : $default;
    }

    public function hasLabel(int $label): bool
    {
        return $this->label === $label;
    }
}

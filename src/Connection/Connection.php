<?php

namespace Phlow\Connection;

use Phlow\Model\RenderableObject;
use Phlow\Model\WorkflowObject;
use Phlow\Node\Node;

class Connection implements WorkflowObject
{
    use RenderableObject {
        __toString as private getClassString;
    }

    private $source;
    private $target;
    private $condition;
    private $label;

    public const LABEL_CHILD = 1;
    public const LABEL_PARENT = 2;
    public const LABEL_NEXT = 3;

    public function __construct(Node $source, Node $target, int $label, $condition = true)
    {
        $source->addOutgoingConnection($this);
        $target->addIncomingConnection($this);

        $this->source = $source;
        $this->target = $target;
        $this->label = $label;
        $this->condition = $condition;
    }

    /**
     * @return Node
     */
    public function getSource(): Node
    {
        return $this->source;
    }

    /**
     * @return Node
     *
     */
    public function getTarget(): Node
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
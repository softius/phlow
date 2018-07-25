<?php

namespace Phlow\Connection;

use Phlow\Expression\Expression;
use Phlow\Model\RenderableObject;
use Phlow\Model\WorkflowObject;
use Phlow\Node\Node;

class Connection implements WorkflowObject
{
    use RenderableObject {
        __toString as private getClassString;
    }

    /**
     * @var Node
     */
    private $source;

    /**
     * @var Node
     */
    private $target;

    /**
     * @var Expression
     */
    private $condition;

    /**
     * @var int
     */
    private $label;

    public const LABEL_CHILD = 1;
    public const LABEL_PARENT = 2;
    public const LABEL_NEXT = 3;

    /**
     * Connection constructor.
     * @param Node $source
     * @param Node $target
     * @param int $label
     * @param Expression|null $condition
     */
    public function __construct(Node $source, Node $target, int $label, Expression $condition = null)
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

    public function getCondition(): Expression
    {
        return $this->condition;
    }

    public function isConditional(): bool
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

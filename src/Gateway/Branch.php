<?php

namespace Phlow\Gateway;

use Phlow\Model\WorkflowNode;
use Phlow\Model\WorkflowNodeTrait;

class Branch implements WorkflowNode
{
    use WorkflowNodeTrait;

    private $condition;

    public function __construct($condition = 'true')
    {
        $this->condition = $condition;
    }

    public function isComposite(): bool
    {
        return true;
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

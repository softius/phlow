<?php

namespace Phlow\Activity;

use Phlow\Model\ExecutableNode;
use Phlow\Model\ExecutableNodeTrait;
use Phlow\Model\RenderableNode;
use Phlow\Model\WorkflowNodeTrait;

/**
 * Class Task
 * A Task is an atomic action.
 * It represents a single unit of work within the Workflow, which usually can not be broken down into further steps.
 * @package Phlow\Activity
 */
class Task implements Activity, ExecutableNode
{
    use WorkflowNodeTrait;
    use ExecutableNodeTrait;
    use RenderableNode;
}

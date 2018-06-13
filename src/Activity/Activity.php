<?php

namespace Phlow\Activity;

use Phlow\Workflow\WorkflowNode;
use Phlow\Workflow\ExecutableNode;

/**
 * Interface Activity
 * Represents any action or task executed by end users or by the application
 * @package Phlow\Activity
 */
interface Activity extends WorkflowNode, ExecutableNode
{
}

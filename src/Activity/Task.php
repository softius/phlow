<?php

namespace Phlow\Activity;

use Phlow\Model\CallbackNodeTrait;
use Phlow\Model\WorkflowNodeTrait;

/**
 * Class Task
 * A Task is an atomic action.
 * It represents a single unit of work within the Workflow, which usually can not be broken down into further steps.
 * @package Phlow\Activity
 */
class Task implements Activity
{
    use WorkflowNodeTrait;
    use CallbackNodeTrait;
}

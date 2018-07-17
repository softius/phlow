<?php

namespace Phlow\Activity;

use Phlow\Model\RenderableObject;
use Phlow\Node\AbstractNode;
use Phlow\Node\Executable;
use Phlow\Node\ExecutableTrait;

/**
 * Class Task
 * A Task is an atomic action.
 * It represents a single unit of work within the Workflow, which usually can not be broken down into further steps.
 * @package Phlow\Activity
 */
class Task extends AbstractNode implements Activity, Executable
{
    use ExecutableTrait;
    use RenderableObject;
}

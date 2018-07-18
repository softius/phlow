<?php

namespace Phlow\Node;

use Phlow\Model\RenderableObject;

/**
 * Class Callback
 * A Callback is an atomic action.
 * It represents a single unit of work within the Workflow, which usually can not be broken down into further steps.
 * @package Phlow\Action
 */
class Callback extends AbstractNode implements Action, Executable
{
    use ExecutableTrait;
    use RenderableObject;
}

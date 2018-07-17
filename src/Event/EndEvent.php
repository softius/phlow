<?php

namespace Phlow\Event;

use Phlow\Model\RenderableObject;
use Phlow\Node\AbstractNode;

/**
 * Class EndEvent
 * Represents the result of the process and indicates that the workflow has reached the completion phase.
 * @package Phlow\Event
 */
class EndEvent extends AbstractNode implements Event
{
    use RenderableObject;
}

<?php

namespace Phlow\Event;

use Phlow\Model\WorkflowNodeTrait;

/**
 * Class ErrorEvent
 * Represents an exception within the workflow which might trigger a different path in workflow execution.
 * @package Phlow\Event
 */
class ErrorEvent implements Event
{
    use WorkflowNodeTrait;
}

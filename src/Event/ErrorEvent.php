<?php

namespace Phlow\Event;

use Phlow\Model\Workflow\WorkflowNodeTrait;

class ErrorEvent implements Event
{
    use WorkflowNodeTrait;
}

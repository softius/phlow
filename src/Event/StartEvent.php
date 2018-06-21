<?php

namespace Phlow\Event;

use Phlow\Model\Workflow\WorkflowNodeTrait;

class StartEvent implements Event
{
    use WorkflowNodeTrait;
}

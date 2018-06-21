<?php

namespace Phlow\Gateway;

use Phlow\Engine\ExpressionEngine;
use Phlow\Model\Workflow\WorkflowNode;
use Phlow\Model\Workflow\WorkflowNodeTrait;

class ExclusiveGateway implements Gateway
{
    use WorkflowNodeTrait;
}

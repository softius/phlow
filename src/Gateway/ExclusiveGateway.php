<?php

namespace Phlow\Gateway;

use Phlow\Model\RenderableObject;
use Phlow\Node\WorkflowNodeTrait;

/**
 * Class ExclusiveGateway
 * Represents alternative flows in a process. Only one of the alternative paths can be taken.
 * @package Phlow\Gateway
 */
class ExclusiveGateway implements Gateway
{
    use WorkflowNodeTrait;
    use RenderableObject;
}

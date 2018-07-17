<?php

namespace Phlow\Node;

use Phlow\Model\RenderableObject;

/**
 * Class Error
 * Represents an exception within the workflow which might trigger a different path in workflow execution.
 * @package Phlow\Node
 */
class Error extends AbstractNode implements Event, ExceptionHandler
{
    use ExceptionHandlerTrait;
    use RenderableObject;
}

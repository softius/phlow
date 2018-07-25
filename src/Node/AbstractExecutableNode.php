<?php

namespace Phlow\Node;

use Phlow\Connection\Connection;
use Phlow\Model\RenderableObject;

class AbstractExecutableNode extends AbstractNode implements Action, Executable
{
    use ExecutableTrait;
    use RenderableObject;
}

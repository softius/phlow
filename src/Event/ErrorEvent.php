<?php

namespace Phlow\Event;

use Phlow\Activity\Task;

class ErrorEvent extends Task
{
    public function __construct($cb)
    {
        parent::__construct($cb);
    }
}
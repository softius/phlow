<?php

namespace Phlow\Event;

class EndEvent implements Event
{
    public function next($message = null)
    {
        throw new \RuntimeException('End event has been reached.');
    }
}

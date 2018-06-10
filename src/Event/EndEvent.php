<?php

namespace Phlow\Event;

class EndEvent implements Event
{
    public function next()
    {
        throw new \RuntimeException('End event has been reached.');
    }
}

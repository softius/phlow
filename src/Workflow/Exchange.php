<?php

namespace Phlow\Workflow;

/**
 * Class Exchange
 * @package Phlow\Workflow
 */
class Exchange
{
    private $inbound;

    private $outbound;

    public function __construct($inbound = null)
    {
        $this->inbound = $inbound;
        $this->outbound = null;
    }

    /**
     * Returns the inbound message
     * @return mixed
     */
    public function in()
    {
        return $this->inbound;
    }

    /**
     * Returns the outbound message, if specified.
     * @return mixed
     */
    public function out()
    {
        return $this->outbound;
    }

    /**
     * Sets the outbound message
     * @param $outbound
     */
    public function setOut($outbound)
    {
        $this->outbound = $outbound;
    }
}
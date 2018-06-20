<?php

namespace Phlow\Engine;

/**
 * Class Exchange
 * @package Phlow\Workflow
 */
class Exchange
{
    private $inbound;

    private $outbound;

    /**
     * Exchange constructor.
     * @param object $inbound
     */
    public function __construct($inbound = null)
    {
        $this->inbound = $inbound;
        $this->outbound = null;
    }

    /**
     * Returns the inbound message
     * @return mixed
     */
    public function getIn()
    {
        return $this->inbound;
    }

    /**
     * Returns the outbound message, if specified.
     * @return mixed
     */
    public function getOut()
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

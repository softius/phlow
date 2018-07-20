<?php

namespace Phlow\Engine;

/**
 * Class Exchange
 * An Exchange is the message container holding the information during the processing of a Workflow Node
 * @package Phlow\Workflow
 */
class Exchange
{
    /**
     * @var null|object Inbound message
     */
    private $inbound;

    /**
     * @var null|object Inbound message
     */
    private $outbound;

    /**
     * @var \Exception The Exception associated with this Exchange
     */
    private $exception;

    /**
     * @var bool
     */
    private $hasOutbound;

    /**
     * Exchange constructor.
     * @param null $inbound
     */
    public function __construct($inbound = null)
    {
        $this->inbound = $inbound;
        $this->outbound = null;
        $this->hasOutbound = false;
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
        $this->hasOutbound = true;
    }

    /**
     * Returns true only and only if an outbound message has been defined
     * @return bool
     */
    public function hasOut(): bool
    {
        return $this->hasOutbound;
    }

    /**
     * Returns the exception associated with this exchange
     * @return \Exception
     */
    public function getException(): ?\Exception
    {
        return $this->exception;
    }

    /**
     * Sets the exception associated with this exchange
     * @param \Exception $exception
     */
    public function setException(\Exception $exception): void
    {
        $this->exception = $exception;
    }

    /**
     * Returns true only and only if an Exception has been associated with this Exchange
     * @return bool
     */
    public function hasException(): bool
    {
        return !empty($this->exception);
    }
}

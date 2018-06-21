<?php

namespace Phlow\Activity;

use Phlow\Model\Workflow\WorkflowNodeTrait;

/**
 * Class Task
 * An atomic event within a workflow.
 * @package Phlow\Activity
 */
class Task implements Activity
{
    use WorkflowNodeTrait;

    /**
     * @var array Callback to be invoked when processing this Task
     */
    private $callback = null;

    /**
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * @param callable $callback
     */
    public function addCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * @return bool
     */
    public function hasCallback(): bool
    {
        return is_callable($this->callback);
    }
}

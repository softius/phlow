<?php

namespace Phlow\Connection;

use Phlow\Engine\ExecutionPath;
use Phlow\Engine\ExecutionPathAwareInterface;
use Phlow\Engine\ExecutionPathAwareTrait;
use Phlow\Node\RecursiveIterator as RecursiveNodeIterator;
use Phlow\Node\Node;

class RecursiveIterator extends \ArrayIterator implements \RecursiveIterator, ExecutionPathAwareInterface
{
    use ExecutionPathAwareTrait;

    /**
     * RecursiveIterator constructor.
     * @param Node $workflowObject
     * @param ExecutionPath $executionPath
     */
    public function __construct(Node $workflowObject, ExecutionPath $executionPath = null)
    {
        $this->setExecutionPath($executionPath);
        $items = [];

        /**
         * @var Connection $connection
         */
        foreach ($workflowObject->getOutgoingConnections(Connection::LABEL_CHILD) as $connection) {
            if (!empty($this->executionPath) && !$this->executionPath->contains($connection)) {
                continue;
            }

            $items[] = $connection;
        }

        parent::__construct($items);
    }

    /**
     * Returns if an iterator can be created for the current entry.
     * @link http://php.net/manual/en/recursiveiterator.haschildren.php
     * @return bool true if the current entry can be iterated over, otherwise returns false.
     * @since 5.1.0
     */
    public function hasChildren()
    {
        return true;
    }

    /**
     * Returns an iterator for the current entry.
     * @link http://php.net/manual/en/recursiveiterator.getchildren.php
     * @return \RecursiveIterator An iterator for the current entry.
     * @since 5.1.0
     */
    public function getChildren()
    {
        return new RecursiveNodeIterator($this->current()->getTarget(), $this->executionPath);
    }
}

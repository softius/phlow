<?php

namespace Phlow\Connection;

use Phlow\Model\RecursiveNodeIterator;
use Phlow\Model\WorkflowNode;

class RecursiveIterator extends \ArrayIterator implements \RecursiveIterator
{
    public function __construct(WorkflowNode $workflowObject)
    {
        $items = [];

        /**
         * @var Connection $connection
         */
        foreach ($workflowObject->getOutgoingConnections(Connection::LABEL_CHILD) as $connection) {
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
        return new RecursiveNodeIterator($this->current()->getTarget());
    }
}

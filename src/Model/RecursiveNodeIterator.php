<?php

namespace Phlow\Model;

class RecursiveNodeIterator extends \ArrayIterator implements \RecursiveIterator
{
    /**
     * RecursiveIterator constructor.
     * @param WorkflowNode $node
     */
    public function __construct(WorkflowNode $node)
    {
        $items = [$node];

        while (!empty($node) && $connections = $node->getOutgoingConnections(WorkflowConnection::LABEL_NEXT)) {
            $node = $connections[0]->getTarget();
            $items[] = $node;
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
        return count($this->current()->getOutgoingConnections(WorkflowConnection::LABEL_CHILD)) > 0;
    }

    /**
     * Returns an iterator for the current entry.
     * @link http://php.net/manual/en/recursiveiterator.getchildren.php
     * @return \RecursiveIterator An iterator for the current entry.
     * @since 5.1.0
     */
    public function getChildren()
    {
        return new RecursiveConnectionIterator($this->current());
    }
}

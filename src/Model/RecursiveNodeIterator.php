<?php

namespace Phlow\Model;

use Phlow\Connection\Connection;
use Phlow\Connection\RecursiveIterator as RecursiveConnectionIterator;

class RecursiveNodeIterator extends \ArrayIterator implements \RecursiveIterator
{
    /**
     * RecursiveIterator constructor.
     * @param WorkflowNode $node
     */
    public function __construct(WorkflowNode $node)
    {
        $items = [$node];

        while (!empty($node) && $node->hasOutgoingConnections(Connection::LABEL_NEXT)) {
            $connections = $node->getOutgoingConnections(Connection::LABEL_NEXT);
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
        return $this->current()->hasOutgoingConnections(Connection::LABEL_CHILD);
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

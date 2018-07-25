<?php

namespace Phlow\Node;

use Phlow\Connection\Connection;
use Phlow\Connection\RecursiveIterator as RecursiveConnectionIterator;

class RecursiveIterator extends \ArrayIterator implements \RecursiveIterator
{
    /**
     * @var callable|null Filter entries by the provided callback
     */
    private $accepts;

    /**
     * RecursiveIterator constructor.
     * @param Node $node
     * @param callable|null $accepts
     */
    public function __construct(Node $node, callable $accepts = null)
    {
        $this->accepts = $accepts;
        $items = [$node];
        while (!empty($node) && $node->hasOutgoingConnections(Connection::LABEL_NEXT)) {
            $connections = $node->getOutgoingConnections(Connection::LABEL_NEXT);
            $node = $connections[0]->getTarget();
            if (!is_callable($accepts) || call_user_func($accepts, $node)) {
                $items[] = $node;
            }
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
        return new RecursiveConnectionIterator($this->current(), $this->accepts);
    }
}

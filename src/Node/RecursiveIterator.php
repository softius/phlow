<?php

namespace Phlow\Node;

use Phlow\Connection\Connection;
use Phlow\Connection\RecursiveIterator as RecursiveConnectionIterator;
use Phlow\Engine\ExecutionPath;
use Phlow\Engine\ExecutionPathAwareInterface;
use Phlow\Engine\ExecutionPathAwareTrait;

class RecursiveIterator extends \ArrayIterator implements \RecursiveIterator, ExecutionPathAwareInterface
{
    use ExecutionPathAwareTrait;

    /**
     * RecursiveIterator constructor.
     * @param Node $node
     * @param ExecutionPath|null $executionPath
     */
    public function __construct(Node $node, ExecutionPath $executionPath = null)
    {
        $this->setExecutionPath($executionPath);

        $items = [$node];
        while (!empty($node) && $node->hasOutgoingConnections(Connection::LABEL_NEXT)) {
            $connections = $node->getOutgoingConnections(Connection::LABEL_NEXT);
            $node = $connections[0]->getTarget();
            if (!empty($this->executionPath) && !$this->executionPath->contains($node)) {
                continue;
            }

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
        return new RecursiveConnectionIterator($this->current(), $this->executionPath);
    }
}

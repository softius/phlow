<?php

namespace Phlow\Model;

class RecursiveIterator implements \RecursiveIterator
{
    /**
     * @var WorkflowNode
     */
    private $start;

    /**
     * @var WorkflowNode
     */
    private $current;

    /**
     * @var $key
     */
    private $key;

    /**
     * @var array
     */
    private $next;

    /**
     * RecursiveIterator constructor.
     * @param WorkflowNode $start
     */
    public function __construct(WorkflowNode $start)
    {
        $this->start = $start;
        $this->rewind();
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        if (empty($this->next)) {
            $this->next = [];
            foreach ($this->current->getOutgoingConnections() as $connection) {
                $this->next[] = $connection->getTarget();
            }
        }

        if (!empty($this->next)) {
            $this->current = array_shift($this->next);
            $this->key++;
        } else {
            $this->current = null;
        }
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return !empty($this->current);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->current = $this->start;
        $this->key = 0;
        $this->next = null;
    }

    /**
     * Returns if an iterator can be created for the current entry.
     * @link http://php.net/manual/en/recursiveiterator.haschildren.php
     * @return bool true if the current entry can be iterated over, otherwise returns false.
     * @since 5.1.0
     */
    public function hasChildren()
    {
        return ($this->current->getOutgoingConnections() > 1);
    }

    /**
     * Returns an iterator for the current entry.
     * @link http://php.net/manual/en/recursiveiterator.getchildren.php
     * @return \RecursiveIterator An iterator for the current entry.
     * @since 5.1.0
     */
    public function getChildren()
    {
        return new RecursiveIterator($this->current);
    }
}

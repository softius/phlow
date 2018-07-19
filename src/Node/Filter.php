<?php

namespace Phlow\Node;

use Phlow\Expression\Expression;
use Phlow\Model\RenderableObject;

/**
 * Class Filter
 * Keeps all the elements of the collection that satisfy the predicate.
 * The order of the elements is preserved.
 * @package Phlow\Node
 */
class Filter extends Callback implements Action
{
    use RenderableObject;

    public function __construct(Expression $filter = null)
    {
        if (empty($filter)) {
            return;
        }

        $this->addCallback(function (iterable $iterable) use ($filter) {
            $iterator = $this->buildIterator($iterable);
            return new \CallbackFilterIterator($iterator, function ($current, $key) use ($filter) {
                return $filter->evaluate(['current' => $current, 'key' => $key]);
            });
        });
    }

    public function addCallback(callable $callback): void
    {
        if ($this->hasCallback()) {
            throw new \BadMethodCallException();
        }

        parent::addCallback($callback);
    }

    /**
     * Helper Method.
     * Returns a Traversable instance.
     * Array are being converted to Iterators using \ArrayIterator
     * @param iterable $iterable
     * @return \Iterator
     */
    private function buildIterator(iterable $iterable): \Iterator
    {
        if ($iterable instanceof \Iterator) {
            return $iterable;
        } elseif ($iterable instanceof \IteratorAggregate) {
            return $iterable->getIterator();
        } elseif (is_array($iterable)) {
            return new \ArrayIterator($iterable);
        }

        throw new \InvalidArgumentException();
    }
}

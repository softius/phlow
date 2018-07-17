<?php

namespace Phlow\Model;

/**
 * Trait RenderableNode
 * Can be used to provide a default implementation for __toString magic method
 * @package Phlow\Model
 */
trait RenderableNode
{
    public function __toString()
    {
        $class = get_class();
        if ($pos = strrpos($class, '\\')) {
            return substr($class, $pos + 1);
        }

        return $class;
    }
}

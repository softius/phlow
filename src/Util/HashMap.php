<?php

namespace Phlow\Util;

class HashMap
{
    /**
     * @var array
     */
    private $map;

    /**
     * Calculate a unique identifier for the contained objects
     * @param $key
     * @return string
     */
    private function getHash($key): string
    {
        return is_object($key) ? spl_object_hash($key) : $key;
    }

    /**
     * Returns true if this map contains no key-value mappings.
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->map);
    }

    /**
     * Associates the specified value with the specified key in this map.
     * @param $key
     * @param $value
     */
    public function put($key, $value)
    {
        $this->map[$this->getHash($key)] = $value;
    }

    /**
     *
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return isset($this->map[$this->getHash($key)]);
    }

    /**
     * Removes the mapping for the specified key from this map if present.
     * @param $key
     */
    public function remove($key)
    {
        if (!$this->exists($key)) {
            throw new \UnderflowException();
        }

        unset($this->map[$this->getHash($key)]);
    }

    /**
     * Returns the value to which the specified key is mapped, or null if this map contains no mapping for the key.
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        if (!$this->exists($key)) {
            throw new \UnderflowException();
        }

        return $this->map[$this->getHash($key)];
    }
}

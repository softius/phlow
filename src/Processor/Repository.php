<?php

namespace Phlow\Processor;

class Repository
{
    /**
     * @var array Mapping between Workflow Nodes and Processors
     */
    private $processors = [];

    public function register(string $nodeClass, string $processorClass): void
    {
        $this->processors[$nodeClass] = $processorClass;
    }

    public function has(string $nodeClass): string
    {
        return array_key_exists($nodeClass, $this->processors);
    }

    public function get(string $nodeClass): string
    {
        return $this->processors[$nodeClass];
    }

    public function getInstance(string $nodeClass): Processor
    {
        $processor = $this->get($nodeClass);
        return new $processor();
    }
}

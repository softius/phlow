<?php

namespace Phlow\Model;

trait WorkflowNodeTrait
{
    private $incomingConnections = [];

    private $outgoingConnections = [];

    public function addOutgoingConnection(WorkflowConnection $connection): void
    {
        $this->outgoingConnections[] = $connection;
    }

    public function addIncomingConnection(WorkflowConnection $connection): void
    {
        $this->incomingConnections[] = $connection;
    }

    public function getOutgoingConnections(int $label = null): array
    {
        return $this->filterConnections($this->outgoingConnections, $label);
    }

    public function getIncomingConnections(int $label = null): array
    {
        return $this->filterConnections($this->incomingConnections, $label);
    }

    private function filterConnections($connections, int $label = null): array
    {
        if (empty($type)) {
            return $connections;
        } else {
            return array_filter($connections, function ($connection) use ($label) {
                return $connection->hasLabel($label);
            });
        }
    }
}

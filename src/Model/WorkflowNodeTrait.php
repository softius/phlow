<?php

namespace Phlow\Model;

use Phlow\Connection\Connection;

trait WorkflowNodeTrait
{
    private $incomingConnections = [];

    private $outgoingConnections = [];

    public function addOutgoingConnection(Connection $connection): void
    {
        $this->outgoingConnections[] = $connection;
    }

    public function addIncomingConnection(Connection $connection): void
    {
        $this->incomingConnections[] = $connection;
    }

    public function getOutgoingConnections(int $label = null): array
    {
        return $this->filterConnections($this->outgoingConnections, $label);
    }

    public function hasOutgoingConnections(int $label = null): bool
    {
        return count($this->getOutgoingConnections($label)) > 0;
    }

    public function getIncomingConnections(int $label = null): array
    {
        return $this->filterConnections($this->incomingConnections, $label);
    }

    public function hasIncomingConnections(int $label = null): bool
    {
        return count($this->getIncomingConnections($label)) > 0;
    }

    private function filterConnections($connections, int $label = null): array
    {
        if (empty($label)) {
            return $connections;
        } else {
            return array_values(
                array_filter(
                    $connections,
                    function (Connection $connection) use ($label) {
                        return $connection->hasLabel($label);
                    }
                )
            );
        }
    }
}

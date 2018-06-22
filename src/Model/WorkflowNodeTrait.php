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

    public function getOutgoingConnections(): array
    {
        return $this->outgoingConnections;
    }

    public function getIncomingConnections(): array
    {
        return $this->incomingConnections;
    }
}

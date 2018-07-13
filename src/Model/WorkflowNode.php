<?php
namespace Phlow\Model;

/**
 * Interface WorkflowNode
 * @package Phlow\Workflow
 */
interface WorkflowNode extends WorkflowObjectDeprecated
{
    public function addOutgoingConnection(WorkflowConnection $connection): void;

    public function addIncomingConnection(WorkflowConnection $connection): void;

    public function getOutgoingConnections(): array;

    public function getIncomingConnections(): array;
}

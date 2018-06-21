<?php
namespace Phlow\Model\Workflow;

/**
 * Interface WorkflowNode
 * @package Phlow\Workflow
 */
interface WorkflowNode
{
    public function addOutgoingConnection(WorkflowConnection $connection): void;

    public function addIncomingConnection(WorkflowConnection $connection): void;

    public function getOutgoingConnections(): array;

    public function getIncomingConnections(): array;
}

<?php
namespace Phlow\Model;

use Phlow\Connection\Connection;

/**
 * Interface WorkflowNode
 * @package Phlow\Workflow
 */
interface WorkflowNode extends WorkflowObject
{
    public function addOutgoingConnection(Connection $connection): void;

    public function addIncomingConnection(Connection $connection): void;

    public function getOutgoingConnections(int $label = null): array;

    public function getIncomingConnections(int $label = null): array;

    public function hasOutgoingConnections(int $label = null): bool;

    public function hasIncomingConnections(int $label = null): bool;
}

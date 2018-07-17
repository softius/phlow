<?php
namespace Phlow\Node;

use Phlow\Connection\Connection;
use Phlow\Model\WorkflowObject;

/**
 * Interface Node
 * @package Phlow\Workflow
 */
interface Node extends WorkflowObject
{
    public function addOutgoingConnection(Connection $connection): void;

    public function addIncomingConnection(Connection $connection): void;

    public function getOutgoingConnections(int $label = null): array;

    public function getIncomingConnections(int $label = null): array;

    public function hasOutgoingConnections(int $label = null): bool;

    public function hasIncomingConnections(int $label = null): bool;
}

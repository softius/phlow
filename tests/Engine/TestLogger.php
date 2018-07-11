<?php

namespace Phlow\Tests\Engine;

use Psr\Log\AbstractLogger;

class TestLogger extends AbstractLogger
{
    private $records = [];

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        array_push($this->records, [
            'level' => $level,
            'message' => $message,
            'context' => $context
        ]);
    }

    public function getAllRecords(): array
    {
        return $this->records;
    }

    public function getLastRecord(): array
    {
        return end($this->records);
    }
}

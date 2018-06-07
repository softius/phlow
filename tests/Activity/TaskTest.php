<?php

namespace Phlow\Tests\Activity;

use Phlow\Activity\Task;
use Phlow\Workflow\Exchange;

class TaskTest extends \PHPUnit\Framework\TestCase {
    public function testSuccess() {
        $nextTask = new Task(function($d) { return $d; });
        $task = new Task(function($d) { return $d; }, $nextTask);

        $task->execute(null);
        $this->assertEquals($nextTask, $task->next());
    }

    public function testException() {
        $exceptionTask = new Task(function($d) { return $d; });
        $task = new Task(function($d) { throw new \Exception(); }, null, $exceptionTask);

        $task->execute(null);
        $this->assertEquals($exceptionTask, $task->next());
    }
}
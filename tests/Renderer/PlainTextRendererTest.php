<?php

namespace Phlow\Tests\Renderer;

use Phlow\Engine\Engine;
use Phlow\Engine\WorkflowInstance;
use Phlow\Renderer\PlainTextRenderer;
use PHPUnit\Framework\TestCase;

class PlainTextRendererTest extends TestCase
{
    public function testRenderSequentialModel()
    {
        $builder = new \Phlow\Model\WorkflowBuilder();
        $workflow = $builder
            ->start()
            ->callback()
            ->callback()
            ->callback()
            ->end()
            ->getWorkflow();

        $expectedOutput = implode(PHP_EOL, [
            '|-Start',
            '|-Callback',
            '|-Callback',
            '|-Callback',
            '\-End',
        ]) . PHP_EOL;
        $actualOutput = $workflow->render(new PlainTextRenderer());
        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testRenderSequentialExecution()
    {
        $builder = new \Phlow\Model\WorkflowBuilder();
        $workflow = $builder
            ->start()
            ->callback()
            ->callback()
            ->callback()
            ->end()
            ->getWorkflow();

        $instance = new WorkflowInstance(new Engine(), $workflow, []);
        $instance->execute();

        $expectedOutput = implode(PHP_EOL, [
                '|-Start',
                '|-Callback',
                '|-Callback',
                '|-Callback',
                '\-End',
            ]) . PHP_EOL;
        $actualOutput = $instance->render(new PlainTextRenderer());
        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testRenderConditionalModel()
    {
        $workflow = (new \Phlow\Model\WorkflowBuilder())
            ->start()
            ->choice()
            ->when('1 == 2')
                ->callback()
            ->otherwise()
                ->callback()
            ->endAll()
            ->getWorkflow();

        $expectedOutput = implode(PHP_EOL, [
            '|-Start',
            '|-Choice',
            '| |-Connection (1 == 2)',
            '| | \-Callback',
            '| \-Connection (true)',
            '|   \-Callback',
            '\-End',
        ]) . PHP_EOL;
        $actualOutput = $workflow->render(new PlainTextRenderer());
        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function testRenderConditionalExecution()
    {
        $workflow = (new \Phlow\Model\WorkflowBuilder())
            ->start()
            ->choice()
            ->when('1 == 2')
            ->callback()
            ->otherwise()
            ->callback()
            ->endAll()
            ->getWorkflow();

        $instance = new WorkflowInstance(new Engine(), $workflow, []);
        $instance->execute();

        $expectedOutput = implode(PHP_EOL, [
                '|-Start',
                '|-Choice',
                '| \-Connection (true)',
                '|   \-Callback',
                '\-End',
            ]) . PHP_EOL;
        $actualOutput = $instance->render(new PlainTextRenderer());
        $this->assertEquals($expectedOutput, $actualOutput);
    }
}

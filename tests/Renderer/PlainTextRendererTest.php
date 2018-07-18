<?php

namespace Phlow\Tests\Renderer;

use Phlow\Renderer\PlainTextRenderer;
use PHPUnit\Framework\TestCase;

class PlainTextRendererTest extends TestCase
{
    public function testRenderSequential()
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

    public function testRenderConditional()
    {
        $workflow = (new \Phlow\Model\WorkflowBuilder())
            ->start()
            ->choice()
            ->when('isEven')
                ->callback()
            ->otherwise()
                ->callback()
            ->endAll()
            ->getWorkflow();

        $expectedOutput = implode(PHP_EOL, [
            '|-Start',
            '|-Choice',
            '| |-Connection (isEven)',
            '| | \-Callback',
            '| \-Connection (true)',
            '|   \-Callback',
            '\-End',
        ]) . PHP_EOL;
        $actualOutput = $workflow->render(new PlainTextRenderer());
        $this->assertEquals($expectedOutput, $actualOutput);
    }
}

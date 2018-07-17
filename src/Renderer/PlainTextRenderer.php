<?php

namespace Phlow\Renderer;

class PlainTextRenderer implements Renderer
{
    public function render(\RecursiveIterator $iterator): string
    {
        $treeIterator = new \RecursiveTreeIterator($iterator);

        $output = null;
        foreach ($treeIterator as $object) {
            $output .= $object . PHP_EOL;
        }

        return $output;
    }
}

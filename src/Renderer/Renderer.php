<?php

namespace Phlow\Renderer;

interface Renderer
{
    public function render(\RecursiveIterator $iterator): string;
}

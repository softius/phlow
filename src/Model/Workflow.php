<?php

namespace Phlow\Model;

/**
 * Class Workflow
 * @package Phlow\Workflow
 */
class Workflow /* implements WorkflowStep */
{
    use WorkflowStepTrait;

    public function isComposite(): bool
    {
        return true;
    }
}

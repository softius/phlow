<?php

namespace Phlow\Activity;

use Phlow\Workflow\WorkflowStep;
use Phlow\Workflow\ExecutableStep;

/**
 * Interface Activity
 * Represents any action or task executed by end users or by the application
 * @package Phlow\Activity
 */
interface Activity extends WorkflowStep, ExecutableStep
{
}

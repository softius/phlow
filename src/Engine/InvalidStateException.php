<?php

namespace Phlow\Engine;

/**
 * Class InvalidStateException
 * Indicates that the execution has reached an invalid state and can not advance further.
 * For instance an End has been already reached or the next Workflow Node is undefined.
 * @package Phlow\Engine
 */
class InvalidStateException extends \RuntimeException
{
}

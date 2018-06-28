# Conditional Flow
The following example demonstrates how to apply conditions and branching in your workflow. As part of the example, we check whether the provided number is less than 100.

``` php
require __DIR__.'/../vendor/autoload.php';

$flow = (new \Phlow\Model\WorkflowBuilder())
    ->start('start', 'choice')
    ->choice('choice')
        ->when('number < 100', 'lessThan100')
        ->otherwise('otherwise')
    ->script('lessThan100', 'end', 'end')
        ->callback(function () {
            print("Number provided was LESS than 100\n");
        })
    ->script('otherwise', 'end', 'end')
        ->callback(function () {
            print("Number provided was EQUAL OR GREATER than 100\n");
        })
    ->end('end')
    ->getWorkflow();

$instance = new \Phlow\Engine\WorkflowInstance($flow, ['number' => 99]);
$instance->advance(); // reach lessThan100 or otherwise
$instance->advance(); // reach end
``` 
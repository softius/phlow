# Conditional Flow
The following example demonstrates how to apply conditions and branching in your workflow. As part of the example, we check whether the provided number is less than 100.

``` php
require __DIR__.'/../vendor/autoload.php';

$flow = (new \Phlow\Model\WorkflowBuilder())
    ->start()
    ->choice()
    ->when('number < 100')
        ->callback(function () {
            print("Number provided was LESS than 100\n");
        })
    ->otherwise()
        ->callback(function () {
            print("Number provided was EQUAL OR GREATER than 100\n");
        })
    ->endAll()
    ->getWorkflow();

$instance = new \Phlow\Engine\WorkflowInstance($flow, ['number' => 99]);
$instance->execute();

// Print the execution path
foreach ($instance->getExecutionPath() as $obj) {
    print get_class($obj) . PHP_EOL;
}
``` 

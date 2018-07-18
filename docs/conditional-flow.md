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

print $flow->render(new \Phlow\Renderer\PlainTextRenderer());
print PHP_EOL;
print $instance->render(new \Phlow\Renderer\PlainTextRenderer());
```

The above code:
* Creates a new workflow
* Executes the workflow
* Displays the model
* Displays the execution path

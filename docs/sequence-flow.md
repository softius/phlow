# Sequence Flow
The following example demonstrates how to execute steps in sequence. As part of the example, we display the sum of two random numbers.

``` php
require __DIR__.'/../vendor/autoload.php';

$flow = (new \Phlow\Model\WorkflowBuilder())
    ->start()
    ->callback(function ($data) {
        $data['a'] = rand(1, 100);
        return $data;
    })
    ->callback(function ($data) {
        $data['b'] = rand(1, 100);
        return $data;
    })
    ->callback(function ($data) {
        $data['sum'] = $data['a'] + $data['b'];
        vprintf("%d + %d = %d\n", $data);
        return $data;
    })
    ->end()
    ->getWorkflow();

$instance = new \Phlow\Engine\WorkflowInstance($flow, []);
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
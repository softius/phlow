# Sequence Flow
The following example demonstrates how to execute tasks in sequence. As part of the example, we display the sum of two random numbers.

``` php
require __DIR__.'/../vendor/autoload.php';

$flow = (new \Phlow\Model\WorkflowBuilder())
    ->start('start', 'random1')
    ->script('random1', 'random2', 'end')
        ->callback(function ($data) {
            $data['a'] = rand(1, 100);
            return $data;
        })
    ->script('random2', 'sum', 'end')
        ->callback(function ($data) {
            $data['b'] = rand(1, 100);
            return $data;
        })
    ->script('sum', 'end', 'end')
        ->callback(function ($data) {
            $data['sum'] = $data['a'] + $data['b'];
            vprintf("%d + %d = %d\n", $data);
            return $data;
        })
    ->end('end')
    ->getWorkflow();

$instance = new \Phlow\Engine\WorkflowInstance($flow, []);
$instance->advance(); // reach random1
$instance->advance(); // reach random2
$instance->advance(); // reach sum
$instance->advance(); // reach end
``` 
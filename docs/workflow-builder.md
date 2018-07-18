# Workflow Builder
Phlow provides a fluent API that can be used to built your Workflows. While it is possible to glue multiple Workflow Nodes together without using the builder, this approach is quite verbose.

The following examples can help you get started with the WorkflowBuilder:

## Initiate a Workflow

The `start` method creates a new instance of StartEvent and appends it to the Workflow. 

``` php
$builder->start();
```

## Execute a callback

The `callback` method creates a new instance of Callback and appends it to the Workflow.

``` php
$builder->callback(function($data) {
    // Do you magic here
    return $data;
});

```

Please note that the callback is left optional on purpose, so the same builder can be used for modeling only. The callback is required only when executing the workflow

``` php
$builder->callback();
```

## Conditional Flow

The `choice` method creates a new instance of Cho and appends it to the Workflow.
The result can be chained with `when` and / or `otherwise` methods, to define the conditional flows.

``` 
$builder
    ->choice()
        ->when('key == 0')
            // Nodes to be executed when key == 0
        ->otherwise()
            // Nodes to be executed when key <> 0
        ->end();
```

## Terminating a Workflow

The `end` method creates a new instance of EndEvent and appends it to the Workflow.

``` php
$builder->end();
```

It is also possible to end all *opened* Node instances by calling `endAll` as below:

``` php
$builder->endAll();
``` 

### Catching Exceptions

The `catch` method creates a new instance of ErrorEvent.

When executing the Workflow, the ErrorEvent will be triggered once an Exception matching the class is provided.

``` php
$builder
    ->catch(\RuntimeException::class)
    ->callback()
    ->end();
```

It is also possible to catch all the exceptions by using the method `catchAll`.

``` php
$builder
    ->catchAll()
    ->callback()
    ->end();
```

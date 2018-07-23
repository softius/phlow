# Workflow Model
A Workflow Model represents the actual process which can be a pipeline, an ETL or a complicated business process. In either case, the Workflow Model specifies all the expected actions and logic that construct the process. Keep in mind that the Workflow Model does not hold information about execution or runtime specifics. In particular, information about the process input, output or even execution path can be found only using the Workflow Engine.

## Build a new workflow

A Workflow Model can be constructed using the Builder fluent API. Here is a simple example to get you started:

``` php
$workflow = (new WorkflowBuilder())
  ->start()
  ->callback(function () {
     // do some cool stuff
  })
  ->end()
  ->getWorkflow();
```

## Render a workflow

A Workflow Model can be visualised which is particularly useful not only for troubleshooting but also when you want to display the model to the end user. Currently it is only possible to render it as plain text but there are plans to add HTML and SVG support in near future. Here is a short example on how you can render the Workflow Model created in the previous example:

``` php
print $workflow->render(new PlainTextRenderer());
```

The above example will output:

```
|-Start
|-Callback
\-End
```

## Error Handling
By default Phlow is completely transparent when it comes to Exceptions, meaning that Phlow will not attempt to catch or modify any raised Exception. On the contrary, any raised Exceptions are left un-caught and it is up to you on how they can be handled.

Here is a short example on how you can model an catch-all error handler using Builder's fluent API. There are other options of course, which can be found in the dedicated section of [Error Handling](error-handling.md)

``` php
$builder
  ->catchAll()
  ->callback(function($e) {
    // handle the exception
  })
  ->end();
```

## Workflow Nodes (Steps)
Each Workflow Model is decomposed to a list of steps, the Workflow Nodes. Workflow Nodes are useful to describe the process and consist of the following tree categories: actions, events and conditionals.

### Events
An event denotes something that happens. 

* **Start**: Acts as a workflow trigger. A workflow can have only one Start step.
* **End**: Represents the result of the process and indicates that the workflow has reached the completion phase.  
* **Error**: Represents an exception within the workflow which might trigger a different path in workflow execution.

### Actions
An action denotes something that must be _done_. It also represents a single unit of work within the workflow, which usually can not be broken down into further steps.

* **Callback**: A callback is a workflow step that can invoke any [PHP callable](http://php.net/manual/en/language.types.callable.php).

### Conditionals
A conditional indicates a point where the outcome of a decision dictates the next step. There can be multiple outcomes, but often there are just two. Based on the decision, the workflow brances to different parts of the flowcharts.

* **Choice**: Represents alternative flows in a process. Only one of the alternative paths can be taken.

## Expressions
Even at the stage of building a new workflow, you will need to define some Expressions especially when working with Conditionals or Collections. The Expression can be defined as a [PHP callable](http://php.net/manual/en/language.types.callable.php) or as a `string` using [Symfony Expression Language](http://symfony.com/doc/current/components/expression_language.html).

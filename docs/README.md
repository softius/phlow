# Concepts
Phlow utilises the notion of workflow to model a process of any kind, through which a piece of work passes from initiation to completion. Each workflow must have a clear starting step (initiation), one or more intermediate steps (execution) and one or more ending steps (completion).

## Workflow Model
A Workflow Model represents the actual process which can be a pipeline, an ETL or a complicated business process. In either case, the Workflow Model specifies all the expected actions and logic that construct the process. Keep in mind that the Workflow Model does not hold information about execution or runtime specifics. In particular, information about the process input, output or even execution path can be found only using the Workflow Engine.

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
Further information and examples are available for the [Workflow Model](workflow-model.md).

## Workflow Engine
Once the Workflow Model bas been constructed, it can be executed by creating a new instance. The instance represents a single execution of the given Workflow Model and holds information about the current status (in progress, completed), the last exchanged message along with the execution path.

During the execution, information is exchanged between each Workflow Node. In particular, each Node accepts an inbound message and produces an outbound message. The initial inbound message can be specified when instantiating the workflow while the last outbound message is considered as the execution's result.

Here is a short example to get you started:

``` php
$instance = new WorkflowInstance($workflow, $input);
$output = $instance->execute();
```

Similar to Workflow Model, you can also visualise the execution path:

``` php
print $instance->render(new PlainTextRenderer());
```

The above example will output:

```
|-Start
|-Callback
\-End
```

Further information and examples are available for the [Workflow Engine](workflow-engine.md).

## Workflow Builder
In the previous sections, we've used the Workflow Builder to create some Workflow Models. The Workflow Builder provides a fluent API that facilates the construction of new models. While it is possible to glue multiple Workflow Nodes together without using the builder, this approach is quite verbose.

Further information and examples are available for the [Workflow Builder](workflow-builder.md).

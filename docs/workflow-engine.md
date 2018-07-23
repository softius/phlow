# Workflow Engine
Once the Workflow Model bas been constructed, it can be executed by creating a new instance. The instance represents a single execution of the given Workflow Model and holds information about the current status (in progress, completed), the last exchanged message along with the execution path.

## Execute a workflow
During the execution, information is exchanged between each Workflow Node. In particular, each Node accepts an inbound message and produces an outbound message. The initial inbound message can be specified when instantiating the process while the last outbound message is considered as the execution's result.

Here is a short example to get you started:

``` php
$instance = new WorkflowInstance($workflow, $input);
$output = $instance->execute();
```

It is also possible to advance the workflow for only one node. In this case, the execution will proceed to the next node and return the generated outbound message.

``` php
$instance = new WorkflowInstance($workflow, $input);
$output = $instance->advance();
```

## Execution Status and Path
At any point, it is possible to access the Execution Path and check execution status, as per below:

``` php
$instance->inProgress(); 
$instance->isCompleted();
```

The Execution Path is a list of Nodes that were covered during the execution. It is possible to render the Execution Path, which is covered in the next section. Depending

``` php
$executionPath = $instance->getExecutionPath();
```

## Render a workflow instance
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

## Exchange & Messages
An Exchange is the message container holding the information during the entire execution of a Workflow. The Exchange consists of the inbound message, the outbound message and the Exception raised, if any.

## Processors
Processors are being used by the Workflow Engine when executing any workflow. Behind the scenes, each Workflow Node is associated with a Processor which is responsible to produce the outbound message and calculate the next step. Currently, the Processors are predefined but there are plans to allow third-party processors that will extend the Phlow.

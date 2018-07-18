# Concepts
Phlow utilises the notion of workflow to model a process of any kind, through which a piece of work passes from initiation to completion. Each workflow must have a clear starting step (initiation), one or more intermediate steps (execution) and one or more ending steps (completion).

Workflow steps are useful to describe the process and consist of the following tree categories: actions, events and conditionals.  

## Events
An event denotes something that happens. 

* **Start**: Acts as a workflow trigger. A workflow can have only one Start step.
* **End**: Represents the result of the process and indicates that the workflow has reached the completion phase.  
* **Error**: Represents an exception within the workflow which might trigger a different path in workflow execution.

## Actions
An action denotes something that must be _done_. It also represents a single unit of work within the workflow, which usually can not be broken down into further steps.

* **Callback**: A callback is a workflow step that can invoke any [PHP callable](http://php.net/manual/en/language.types.callable.php).

## Conditionals
A conditional indicates a point where the outcome of a decision dictates the next step. There can be multiple outcomes, but often there are just two. Based on the decision, the workflow brances to different parts of the flowcharts.

* **Choice**: Represents alternative flows in a process. Only one of the alternative paths can be taken.

## Exchange
An Exchange is the message container holding the information during the entire execution of a Workflow. Each new Workflow instance an initial dataset which can be passed to create the first Exchange.

## Processors
Processors are being used by the Phow Engine when executing any workflow. Each workflow step is associated with a Processor which is responsible to modify the Exchange and/or calculate the next step.

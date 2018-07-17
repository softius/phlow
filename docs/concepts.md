# Concepts
Phlow utilises the notion of workflow to model a process of any kind, through which a piece of work passes from initiation to completion. Each workflow must have a clear starting step (initiation), one or more intermediate steps (execution) and one or more ending steps (completion).

Workflow steps are useful to describe the process and consist of the following tree categories: activities, events and gateways.  

## Events
An event denotes something that happens. 

* **Start Node**: Acts as a workflow trigger. A workflow can have only Start Node.
* **End Node**: Represents the result of the process and indicates that the workflow has reached the completion phase.  
* **Error Node**: Represents an exception within the workflow which might trigger a different path in workflow execution.

## Activities
An activity denotes something that must be _done_.

* **Task**: A task is an atomic workflow step. It represents a single unit of work within the workflow, which usually can not be broken down into further steps.

## Gateways
A gateway denotes forking and merging of workflow paths. 

* **Exclusive Node**: Represents alternative flows in a process. Only one of the alternative paths can be taken.

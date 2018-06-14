# Phlow
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

Phlow is a workflow engine for PHP. Heavily inspired by [BPMN 2.0][link-bpmn2], this project attempts to provide a solution to represent and implement business processes in PHP projects.

Phlow is a framework agnostic solution. It utilises the notion of workflow to model a process of any kind, through which a piece of work passes from initiation to completion.

<img src="https://raw.githubusercontent.com/softius/Phlow/docs/docs/article-approval.svg?sanitize=true">

## Installation

Phlow can be installed to your PHP project by executing the following composer command. Please note that currently there is no stable version available yet.

``` bash
$ composer require softius/phlow:dev-master
```

## Usage
[...]

## Concepts
Phlow utilises the notion of workflow to model a process of any kind, through which a piece of work passes from initiation to completion. Each workflow must have a clear starting step (initiation), one or more intermediate steps (execution) and one or more ending steps (completion).

Workflow steps are useful to describe the process and consist of the following tree categories: activities, events and gateways.  

### Events
An event denotes something that happens. 

* **Start Event**: Acts as a workflow trigger. A workflow can have only Start Event.
* **End Event**: Represents the result of the process and indicates that the workflow has reached the completion phase.  
* **Error Event**: Represents an exception within the workflow which might trigger a different path in workflow execution.

### Activities
An activity denotes something that must be _done_.

* **Task**: A task is an atomic workflow step. It represents a single unit of work within the workflow, which usually can not be broken down into further steps.

### Gateways
A gateway denotes forking and merging of workflow paths. 

* **Exclusive Gateway**: Represents alternative flows in a process. Only one of the alternative paths can be tasken.
 
## Roadmap

### v1.0
- [x] Exclusive Gateway
- [ ] Connections
- [x] Workflow API
- [x] Expressions
- [ ] Worfklow Engine
- [ ] PSR-3 Logging

### v1.x
- [ ] Persistent Workflow
- [ ] HTML Workflow Visualiation
- [ ] SVG Workflow Visualiation
- [ ] Metrics
 
## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email softius@gmail.com instead of using the issue tracker.

## Credits

- [Iacovos Constantinou][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/softius/phlow.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/softius/Phlow/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/softius/phlow.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/softius/phlow
[link-travis]: https://travis-ci.org/softius/phlow
[link-downloads]: https://packagist.org/packages/softius/phlow
[link-author]: https://github.com/softius
[link-contributors]: ../../contributors
[link-bpmn2]: http://www.bpmn.org/

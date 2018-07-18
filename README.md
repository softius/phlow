# Phlow
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
![Code Climate][ico-maintenance]
![Code Climate][ico-coverage]


Phlow is a workflow modeller and engine for PHP. Heavily inspired by [BPMN 2.0][link-bpmn2] and [Apache Camel][link-apache-camel], Phlow attempts to provide a library to design and implement business processes in PHP projects. It utilises the notion of workflow to model a process of any kind, through which a piece of work passes from initiation to completion.

Phlow process models can be created using PHP. No third party tools are required to model and/or execute your process.  

Phlow is a framework agnostic solution.

## Features
:white_check_mark: Sequence flow\
:white_check_mark: Conditional flow\
:white_check_mark: Error handling\
:white_check_mark: Callbacks\
:white_check_mark: Boolean expressions for Conditions\
:white_check_mark: PSR/3 Logger integration\
:white_check_mark: Execution path replay\
:white_check_mark: Workflow model in plain/text format\
:white_check_mark: Execution path in plain/text format

See the [Roadmap][link-roadmap] for more information about the upcoming releases.

## Getting Started
The following image illustrates a simple process for dealing with a non-functioning lamp. Once a non-functioning lamp is found, the flow evaluates whether the lamp is plugged in.  If not, it evaluates whether the lamp has been burned out. In any case, particular actions must be taken i.e. replace the lamp.

[![A simple flowchart for troubleshooting a broken lamp.][img-lamp-flowchart]][link-lamp-flowchart]

Also, the following code illustrates the model for the same process. 

``` php
$builder = new WorkflowBuilder();
$builder
  ->start()
  ->choice()
  ->when('isPluggedIn')
    ->callback()          // Plug in lamp
  ->when('isBurnedOut')
    ->callback()          // Replace lamp
  ->otherwise()
    ->callback()          // Repair lamp
  ->endAll()
```

Once the model bas been built, it can be executed by creating a new instance. At this point it is possible to pass some data that could be made available throughout the process. The data can be any object which could be also updated as part of the process.

``` php
$workflow = $builder->getWorkflow();
$instance = new WorkflowInstance($workflow, $data);
$instance->execute();
```

## Installation

Phlow can be installed to your PHP project by executing the following composer command. Please note that currently there is no stable version yet available.

``` bash
$ composer require softius/phlow 0.3.0
```

## Documentation
* [Concepts][link-concepts]
* [Workflow Builder][link-workflow-builder]
* [Sequence Flow - Example][link-sequence-flow]
* [Conditional Flow - Example][link-conditional-flow]
* [Error Handling][link-error-handling]
* [FAQs][link-faqs]
 
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

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/softius/Phlow.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/softius/Phlow/master.svg?style=flat-square
[ico-maintenance]: https://img.shields.io/codeclimate/maintainability/softius/Phlow.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/softius/phlow.svg?style=flat-square
[ico-coverage]: https://img.shields.io/codeclimate/coverage-letter/softius/Phlow.svg?style=flat-square

[img-lamp-flowchart]: https://upload.wikimedia.org/wikipedia/commons/9/91/LampFlowchart.svg

[link-packagist]: https://packagist.org/packages/softius/phlow
[link-travis]: https://travis-ci.org/softius/phlow
[link-downloads]: https://packagist.org/packages/softius/phlow
[link-author]: https://github.com/softius
[link-contributors]: ../../contributors
[link-bpmn2]: http://www.bpmn.org/
[link-apache-camel]: http://camel.apache.org
[link-concepts]: https://github.com/softius/Phlow/blob/master/docs/concepts.md
[link-roadmap]: https://github.com/softius/Phlow/blob/master/docs/roadmap.md
[link-faqs]: https://github.com/softius/Phlow/blob/master/docs/faqs.md
[link-workflow-builder]: https://github.com/softius/Phlow/blob/master/docs/workflow-builder.md
[link-sequence-flow]: https://github.com/softius/Phlow/blob/master/docs/sequence-flow.md
[link-conditional-flow]: https://github.com/softius/Phlow/blob/master/docs/conditional-flow.md
[link-error-handling]: https://github.com/softius/Phlow/blob/master/docs/error-handling.md
[link-lamp-flowchart]: https://en.wikipedia.org/wiki/File:LampFlowchart.svg

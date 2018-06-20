# Phlow
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

Phlow is a workflow modeller and engine for PHP. Heavily inspired by [BPMN 2.0][link-bpmn2], Phlow attempts to provide a library to design and implement business processes in PHP projects. It utilises the notion of workflow to model a process of any kind, through which a piece of work passes from initiation to completion.

Phlow process models can be created using PHP. No third party tools are required to model and/or execute your process.  

Phlow is a framework agnostic solution.

## Getting Started
The following image illustrates a simple approval process. Once the author composes a new article, the article gets reviewed by the reviewer. As the result of the review, the reviewer can request further updates or publish the article.

<img src="https://raw.githubusercontent.com/softius/Phlow/docs/docs/article-approval.svg?sanitize=true">

Also, the following code illustrates the model for the same process. 

``` php
$builder = new WorkflowBuilder();
$builder
  ->start('Start', 'ComposeArticle')
  ->task('ComposeArticle', 'ReviewArticle')
  ->task('ReviewArticle')
  ->choice('IsApproved')
    ->when('approved == true', 'PublishArticle')
    ->when('approved == false', 'UpdateArticle')
  ->task('UpdateArticle', 'ReviewArticle')
  ->task('PublishArticle', 'End')
  ->end('End');
```

Once the model bas been built, it can be executed by creating a new instance. At this point it is possible to pass some data that would be made available throughout the process. The data can be any object which could be also updated as part of the process.

``` php
$workflow = $builder->getWorkflow();
$instance = new WorkflowInstance($workflow, $data);
$instance->advance();
```

## Installation

Phlow can be installed to your PHP project by executing the following composer command. Please note that currently there is no stable version available yet.

``` bash
$ composer require softius/phlow:dev-master
```

## Documents
* [Concepts][link-concepts]
 
## Roadmap

### v1.0
- [x] Exclusive Gateway
- [ ] Connections
- [x] Workflow API
- [x] Expressions
- [x] Worfklow Engine
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

The MIT License (MIT). Please see [License File](LICENSE) for more information.

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
[link-concepts]: https://github.com/softius/Phlow/blob/docs/docs/concepts.md

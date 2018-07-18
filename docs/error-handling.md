# Error handling
By default Phlow is completely transparent when it comes to Exceptions, meaning that Phlow will not modify any raised Exception.
On the contrary, any raised exceptions are left un-caught and it is up to you on how they can be handled.

There are two options to catch and handle Exceptions.

## PHP try-catch
Of course, the first option is to wrap the `execute` method in a try-catch block. 

``` php
// Setup your workflow
try {
  $workflow->execut();
} catch (\Exception $e) {
  // Handle the exception here
}
```

## `catch`, `catchAll` methods
Another option is to define an error handler using Workflow Builder. This will allow you to define an alternative execution path when an Exception is raised.
In such case, the raised Exception will be caught by Phlow and attached to the newly created Error.

``` php
$builder
  ->catchAll()
    ->callback()
    ->end();

```

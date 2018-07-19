# Collections
The following operations can be applied when working with Collections.
By Collections we refer to any variable that it is an array or an object implementing Traversable

## Filter
By using `filter`, you can exclude elements from the collection that do not satisfy the given predicate (boolean expression).
In order words, the result of a `filter` will be all the elements that satisfy the given predicate.

You can specify a PHP callable as a predicate.
The callback should accept two arguments: the current item and the current key.
In the following example we keep all the numbers less than 50.

``` php
$builder
  ->start()
  ->filter(function ($current, $key) {
    return $current < 50;
  })
  ->end();
```

The above example can rewritten using Simple expressions, as follows:

``` php
$builder
  ->start()
  ->filter('current < 25')
  ->end();
```
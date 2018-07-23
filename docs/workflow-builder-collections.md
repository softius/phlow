# Collections
The following operations can be applied when working with Collections.
By Collections we refer to any variable that it is an array or an object implementing Traversable

## Filter
The `filter` method removes all the elements that do not satisfy the given predicate (boolean expression).
Upon processing, the Exchange will hold a lazy Collection.

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

## First
The `first` method keeps only the first element of the collection.
Upon processing the Exchange will hold a single element (not a Collection).

No configuration is required for `first` method. 

``` php
$builder
  ->start()
  ->first()
  ->end();
```

## Last
Similar to `first`, the `last` method keeps only the last element of the collection.
Upon processing the Exchange will hold a single element (not a Collection).

No configuration is required for `last` method. 

``` php
$builder
  ->start()
  ->last()
  ->end();
```

## Find
The `find` method keeps only the first element that satisfies the predicate.
Upon processing, the Exchange will hold a single element (not a Collection).

You can specify a PHP callable as a predicate.
The callback should accept two arguments: the current item and the current key.
In the following example we keep all the numbers less than 50.

``` php
$builder
  ->start()
  ->find(function ($current, $key) {
    return $current < 50;
  })
  ->end();
```

The above example can rewritten using Simple expressions, as follows:

``` php
$builder
  ->start()
  ->find('current < 25')
  ->end();
```

## Sort
The `sort` method sorts the collection according to the provided expression (compare function).
Upon processing, the Exchange will hold a non-lazy Collection.

You can specify a PHP callable as a predicate.
The callback should accept two arguments: the current item and the current key.
In the following example we keep all the numbers less than 50.

``` php
$builder
  ->start()
  ->sort(function ($a, $b) {
    return $a - $b;
  })
  ->end();
```

The above example can rewritten using Simple expressions, as follows:

``` php
$builder
  ->start()
  ->sort('a - b')
  ->end();
```

## Map
The `map` method iterates through the collection and passed each value to the given callback.
The callback must return the new instance of the item, thus creating a new collection of items.

You can specify a PHP callable as a predicate.
The callback should accept two arguments: the current item and the current key.
In the following example we keep all the numbers less than 50.

``` php
$builder
  ->start()
  ->map(function ($current, $key) {
    return $current * 10;
  })
  ->end();
```

The above example can rewritten using Simple expressions, as follows:

``` php
$builder
  ->start()
  ->map('current * 10')
  ->end();
```

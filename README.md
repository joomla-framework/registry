# The Registry Package [![Build Status](https://ci.joomla.org/api/badges/joomla-framework/registry/status.svg?ref=refs/heads/2.0-dev)](https://ci.joomla.org/joomla-framework/registry)

[![Latest Stable Version](https://poser.pugx.org/joomla/registry/v/stable)](https://packagist.org/packages/joomla/registry)
[![Total Downloads](https://poser.pugx.org/joomla/registry/downloads)](https://packagist.org/packages/joomla/registry)
[![Latest Unstable Version](https://poser.pugx.org/joomla/registry/v/unstable)](https://packagist.org/packages/joomla/registry)
[![License](https://poser.pugx.org/joomla/registry/license)](https://packagist.org/packages/joomla/registry)

The Registry package provides an indexed key-value data store and an API for importing/exporting this data to several formats.

## Load config by Registry

``` php
use Joomla\Registry\Registry;

$registry = new Registry;

// Load by string
$registry->loadString('{"foo" : "bar"}');

$registry->loadString('<root></root>', 'xml');

// Load by object or array
$registry->loadObject($object);
$registry->loadArray($array);

// Load by file
$registry->loadFile($root . '/config/config.json', 'json');
```

## Accessing a Registry by getter & setter

### Get value

``` php
$registry->get('foo');

// Get a non-exists value and return default
$registry->get('foo', 'default');

// OR

$registry->get('foo') ?: 'default';
```

### Set value

``` php
// Set value
$registry->set('bar', $value);

// Sets a default value if not already assigned.
$registry->def('bar', $default);
```

### Accessing children value by path

``` php
$json = '{
	"parent" : {
		"child" : "Foo"
	}
}';

$registry = new Registry($json);

$registry->get('parent.child'); // return 'Foo'

$registry->set('parent.child', $value);
```

## Removing values from Registry

``` php
// Set value
$registry->set('bar', $value);

// Remove the key
$registry->remove('bar');

// Works for nested keys too
$registry->set('nested.bar', $value);
$registry->remove('nested.bar');
```

## Accessing a Registry as an Array

The `Registry` class implements `ArrayAccess` so the properties of the registry can be accessed as an array. Consider the following examples:

``` php
// Set a value in the registry.
$registry['foo'] = 'bar';

// Get a value from the registry;
$value = $registry['foo'];

// Check if a key in the registry is set.
if (isset($registry['foo']))
{
	echo 'Say bar.';
}
```

## Merge Registry

#### Using load* methods to merge two config files.

``` php
$json1 = '{
    "field" : {
        "keyA" : "valueA",
        "keyB" : "valueB"
    }
}';

$json2 = '{
    "field" : {
        "keyB" : "a new valueB"
    }
}';

$registry->loadString($json1);
$registry->loadString($json2);
```

Output

```
Array(
    field => Array(
        keyA => valueA
        keyB => a new valueB
    )
)
```

#### Merge another Registry

``` php
$object1 = '{
	"foo" : "foo value",
	"bar" : {
		"bar1" : "bar value 1",
		"bar2" : "bar value 2"
	}
}';

$object2 = '{
	"foo" : "foo value",
	"bar" : {
		"bar2" : "new bar value 2"
	}
}';

$registry1 = new Registry(json_decode($object1));
$registry2 = new Registry(json_decode($object2));

$registry1->merge($registry2);
```

If you just want to merge first level, do not hope recursive:

``` php
$registry1->merge($registry2, false); // Set param 2 to false that Registry will only merge first level
```

## Dump to one dimension

``` php
$array = array(
    'flower' => array(
        'sunflower' => 'light',
        'sakura' => 'samurai'
    )
);

$registry = new Registry($array);

// Make data to one dimension

$flatted = $registry->flatten();

print_r($flatted);
```

The result:

```
Array
(
    [flower.sunflower] => light
    [flower.sakura] => samurai
)
```

## Installation via Composer

Add `"joomla/registry": "~2.0"` to the require block in your composer.json and then run `composer install`.

```json
{
	"require": {
		"joomla/registry": "~2.0"
	}
}
```

Alternatively, you can simply run the following from the command line:

```sh
composer require joomla/registry "~2.0"
```

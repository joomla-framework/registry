## Overview

The Registry package provides an indexed key-value data store and an API for importing/exporting this data to several formats.

### Basic usage

The following demonstrates basic usage for storing and retrieving data from the store.

``` php
use Joomla\Registry\Registry;

$registry = new Registry;

// Set a value in the registry.
$registry->set('foo', 'bar');

// Get a value from the registry;
$value = $registry->get('foo');
```

### Format Support

A Registry supports import/export in several formats, including:

- INI
- JSON
- PHP
- XML
- YAML (requires [Symfony's Yaml Component](https://symfony.com/doc/current/components/yaml.html))

#### Export to file

To export the registry to a file, you first convert it to a string with the `toString()` method or typecast it (i.e. `(string) $registry`).
The method accepts two parameters; a format type (defaulting to `JSON`) and an options array to pass to the formatter. The result of this
conversion can then be written to the filesystem.

``` php
use Joomla\Registry\Registry;

$registry = new Registry(['foo' => 'bar']);

// Dump the registry to a file
file_put_contents(__DIR__ . '/registry.json', $registry->toString());
```

#### Import from file

Likewise, a registry can be imported from a file by the `loadFile()` method. The method accepts three parameters; the file name (required),
a format type (defaulting to `JSON`), and an options array to pass to the formatter.

``` php
use Joomla\Registry\Registry;

$registry = new Registry;

// Load the previously dumped registry
$registry->loadFile(__DIR__ . '/registry.json');
```

#### XML Structure

Keep in mind that due to XML complexity, a special format must be kept when loading into Registry. By default, the parent XML element should
be named "registry" and all child elements should be named "node". The nodes should include a "name" attribute, for the name of the value.
The nodes can be optionally filtered with a "type" attribute. Valid types are:

* array
* boolean
* double
* integer
* object (default)
* string

##### Example XML Document

``` xml
<?xml version="1.0"?>
<registry>
	<node name="foo_1" type="string">bar</node>
	<node name="foo_2" type="boolean">1</node>
	<node name="foo_3" type="integer">42</node>
	<node name="foo_4" type="double">3.1415</node>
	<node name="foo_5" type="object">
		<node name="foo_5_a" type="string">value</node>
	</node>
	<node name="foo_6" type="array">
		<node name="foo_6_a" type="string">value</node>
	</node>
</registry>
```

The names of the XML import nodes can be customised using options. For example:

``` php
$registry = new Registry(
    [
        'name' => 'data',
        'nodeName' => 'value'
    ]
);

$registry->loadString('<data><value name="foo" type="string">bar</value></data>, 'xml');
```

#### Custom Formats
To load a custom format you must implement the `Joomla\Registry\FormatInterface`. This can then be loaded through the `Joomla\Registry\Factory`
class. To load a custom format not provided by Joomla then you can load it by using `Factory::getFormat($type, $options)`. In this scenario
`$type` contains the format name, from which the class name is built.

By default, formats use the `Joomla\Registry\Format` namespace. For a custom format class, the `format_namespace` key should be passed in the
`$options` array which contains the namespace that the format is in. This will also allow you to create a custom format for one of the format
types provided by this package.

### Accessing a Registry as an array

The `Registry` class implements [ArrayAccess](https://secure.php.net/manual/en/class.arrayaccess.php) so the properties of the registry can
be accessed as an array. Consider the following examples:

``` php
use Joomla\Registry\Registry;

$registry = new Registry;

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

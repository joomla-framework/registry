<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests;

use Joomla\Registry\Registry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Test class for \Joomla\Registry\Registry.
 */
class RegistryTest extends TestCase
{
	/**
	 * @testdox  A Registry instance is instantiated with empty data
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testARegistryInstanceIsInstantiatedWithEmptyData()
	{
		$this->assertCount(0, new Registry, 'The Registry data store should be empty.');
	}

	/**
	 * @testdox  A Registry instance is instantiated with an array of data
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testARegistryInstanceIsInstantiatedWithAnArrayOfData()
	{
		$this->assertCount(1, new Registry(['foo' => 'bar']), 'The Registry data store should not be empty.');
	}

	/**
	 * @testdox  A Registry instance is instantiated with a string of data
	 *
	 * @covers   Joomla\Registry\Registry
	 * @uses     Joomla\Registry\Factory
	 * @uses     Joomla\Registry\Format\Json
	 */
	public function testARegistryInstanceIsInstantiatedWithAStringOfData()
	{
		$this->assertCount(1, new Registry(json_encode(['foo' => 'bar'])), 'The Registry data store should not be empty.');
	}

	/**
	 * @testdox  A Registry instance is instantiated with another Registry
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testARegistryInstanceIsInstantiatedWithAnotherRegistry()
	{
		$this->assertCount(1, new Registry(new Registry(['foo' => 'bar'])), 'The Registry data store should not be empty.');
	}

	/**
	 * @testdox  A Registry instance instantiated with a string of data is correctly manipulated
	 *
	 * @covers   Joomla\Registry\Registry
	 * @uses     Joomla\Registry\Factory
	 * @uses     Joomla\Registry\Format\Json
	 */
	public function testARegistryInstanceInstantiatedWithAStringOfDataIsCorrectlyManipulated()
	{
		$a = new Registry(json_encode(['foo' => 'bar', 'goo' => 'car', 'nested' => ['foo' => 'bar', 'goo' => 'car']]));

		// Check top level values
		$this->assertSame('bar', $a->get('foo'));
		$this->assertSame('bar', $a->def('foo'));
		$this->assertSame('far', $a->set('foo', 'far'));

		// Check nested values
		$this->assertSame('bar', $a->get('nested.foo'));
		$this->assertSame('bar', $a->def('nested.foo'));
		$this->assertSame('far', $a->set('nested.foo', 'far'));

		// Check adding a new nested object
		$a->set('new.nested', ['foo' => 'bar', 'goo' => 'car']);
		$this->assertSame('bar', $a->get('new.nested.foo'));
		$this->assertSame('bar', $a->def('new.nested.foo'));
		$this->assertSame('far', $a->set('new.nested.foo', 'far'));
	}

	/**
	 * @testdox  A Registry instance can be cloned
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testCloningARegistry()
	{
		$a = new Registry(['a' => '123', 'b' => '456']);
		$b = clone $a;

		$this->assertSame(serialize($a), serialize($b), 'A cloned Registry should have the same serialized contents as the original.');
		$this->assertNotSame($a, $b, 'A cloned Registry should be a different object from the original.');
	}

	/**
	 * @testdox  A Registry instance can be cast as a string
	 *
	 * @covers   Joomla\Registry\Registry
	 * @uses     Joomla\Registry\Factory
	 * @uses     Joomla\Registry\Format\Json
	 */
	public function testConvertingARegistryToAString()
	{
		$a = new Registry(['foo' => 'bar']);

		// Registry::toString() defaults to JSON output
		$this->assertSame((string) $a, '{"foo":"bar"}', 'The magic __toString method should return a JSON formatted Registry.');
	}

	/**
	 * @testdox  A Registry instance can be counted
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testCountable()
	{
		$a = new Registry(
			[
				'foo1' => 'testtoarray1',
				'foo2' => 'testtoarray2',
				'config' => [
					'foo3' => 'testtoarray3'
				]
			]
		);

		$this->assertCount(3, $a, 'count() should correctly count the number of data elements.');
	}

	/**
	 * @testdox  A Registry instance can be processed through json_encode()
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testJsonSerializingARegistry()
	{
		$this->assertSame(json_encode(new Registry(['foo' => 'bar'])), '{"foo":"bar"}', 'A Registry\'s data should be encoded to JSON.');
	}

	/**
	 * @testdox  A default value is assigned to a key if not already set
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testDefineADefaultValueIfKeyIsNotSet()
	{
		$this->assertSame((new Registry)->def('foo', 'bar'), 'bar', 'Calling def() on an unset key should assign the specified default value.');
	}

	/**
	 * @testdox  A default value is not assigned to a key if already set
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testDoNotDefineADefaultValueIfKeyIsSet()
	{
		$this->assertSame((new Registry(['foo' => 'bar']))->def('foo', 'car'), 'bar', 'Calling def() on a key with a value should return the current value.');
	}

	/**
	 * @testdox  The Registry validates top level keys exist
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testEnsureTopLevelKeysExist()
	{
		$a = new Registry(['foo' => 'bar']);

		$this->assertTrue($a->exists('foo'), 'The top level key "foo" should exist.');
		$this->assertFalse($a->exists('goo'), 'The top level key "goo" should not exist.');
	}

	/**
	 * @testdox  The Registry validates nested keys exist
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testEnsureNestedKeysExist()
	{
		$a = new Registry(['nested' => ['foo' => 'bar']]);

		$this->assertTrue($a->exists('nested.foo'), 'The nested key "nested.foo" should exist.');
		$this->assertFalse($a->exists('nested.goo'), 'The nested key "nested.goo" should not exist.');
	}

	/**
	 * @testdox  The Registry does not validate an empty path exists
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testEnsureEmptyPathsDoNotExist()
	{
		$this->assertFalse((new Registry)->exists(''), 'An empty path should not exist.');
	}

	/**
	 * @testdox  The Registry returns the default value when a key is not set.
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testGetReturnsTheDefaultValueWhenAKeyIsNotSet()
	{
		$this->assertNull((new Registry)->get('foo'), 'The default value should be returned for an unassigned key.');
	}

	/**
	 * @testdox  The Registry returns the default value when a nested key is not set.
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testGetReturnsTheDefaultValueWhenANestedKeyIsNotSet()
	{
		$this->assertNull((new Registry(['nested' => (object) ['foo' => 'bar']]))->get('nested.goo'), 'The default value should be returned for an unassigned nested key.');
	}

	/**
	 * @testdox  The Registry returns the default value when the path is empty.
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testGetReturnsTheDefaultValueWhenThePathIsEmpty()
	{
		$this->assertNull((new Registry)->get(''), 'The default value should be returned for an empty path.');
	}

	/**
	 * @testdox  The Registry returns the assigned value when a key is set.
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testGetReturnsTheAssignedValueWhenAKeyIsSet()
	{
		$this->assertSame((new Registry(['foo' => 'bar']))->get('foo'), 'bar', 'The value of "foo" should be returned.');
	}

	/**
	 * @testdox  The Registry returns the assigned value for a set nested key.
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testGetReturnsTheAssignedValueForASetNestedKey()
	{
		$this->assertSame((new Registry(['nested' => (object) ['foo' => 'bar']]))->get('nested.foo'), 'bar', 'The value of "nested.foo" should be returned.');
	}

	/**
	 * @testdox  The Registry correctly handles assignments for integer zero.
	 *
	 * @covers   Joomla\Registry\Registry
	 * @ticket   https://github.com/joomla/jissues/issues/629
	 */
	public function testTheRegistryCorrectlyHandlesAssignmentsForIntegerZero()
	{
		$a = new Registry;
		$a->set('foo', 0);
		$a->set('goo.bar', 0);

		$this->assertSame(0, $a->get('foo'), 'The Registry correctly handles when a top level key has a value of 0');
		$this->assertSame(0, $a->get('goo.bar'), 'The Registry correctly handles when a nested key has a value of 0');
	}

	/**
	 * @testdox  The Registry correctly handles assignments for class instances.
	 *
	 * @covers   Joomla\Registry\Registry
	 * @ticket   https://github.com/joomla-framework/registry/issues/8
	 */
	public function testTheRegistryCorrectlyHandlesAssignmentsForClassInstances()
	{
		$class = new class {};

		$a = new Registry;
		$a->set('class', $class);
		$a->set('nested.class', $class);

		$this->assertSame($class, $a->get('class'), 'The Registry correctly handles when a top level key is an instance of a class');
		$this->assertSame($class, $a->get('nested.class'), 'The Registry correctly handles when a nested key is an instance of a class');
	}

	/**
	 * @testdox  The Registry can be iterated
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testTheRegistryCanBeIterated()
	{
		$this->assertInstanceOf('ArrayIterator', (new Registry)->getIterator());
	}

	/**
	 * @testdox  The Registry can load an array
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testAnArrayCanBeLoaded()
	{
		$registry = new Registry;

		$this->assertSame($registry->loadArray(['foo' => 'bar']), $registry, 'The loadArray() method should return $this');
		$this->assertSame('bar', $registry->get('foo'), 'The array\'s data should be correctly loaded.');
	}

	/**
	 * @testdox  The Registry can load a flattened array
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testAFlattenedArrayCanBeLoaded()
	{
		$array = [
			'foo.bar'  => 1,
			'foo.test' => 2,
			'bar'      => 3
		];
		$registry = new Registry;

		$this->assertSame($registry->loadArray($array, true), $registry, 'The loadArray() method should return $this');
		$this->assertSame(1, $registry->get('foo.bar'), 'The flattened array\'s data should be correctly loaded.');
	}

	/**
	 * @testdox  The Registry can load an object
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testAnObjectCanBeLoaded()
	{
		$object = new \stdClass;
		$object->foo = 'testloadobject';

		$registry = new Registry;
		$result = $registry->loadObject($object);

		$this->assertSame($registry->loadObject($object), $registry, 'The loadObject() method should return $this');
		$this->assertSame('testloadobject', $registry->get('foo'), 'The object\'s data should be correctly loaded.');
	}

	/**
	 * @testdox  The Registry can load a file
	 *
	 * @covers   Joomla\Registry\Registry
	 * @uses     Joomla\Registry\Factory
	 * @uses     Joomla\Registry\Format\Json
	 */
	public function testAFileCanBeLoaded()
	{
		$registry = new Registry;

		$this->assertSame($registry->loadFile(__DIR__ . '/Stubs/jregistry.json'), $registry, 'The loadFile() method should return $this');
		$this->assertSame('bar', $registry->get('foo'), 'The file\'s data should be correctly loaded.');
	}

	/**
	 * @testdox  The Registry can load a string
	 *
	 * @covers   Joomla\Registry\Registry
	 * @uses     Joomla\Registry\Factory
	 * @uses     Joomla\Registry\Format\Ini
	 */
	public function testAStringCanBeLoaded()
	{
		$registry = new Registry;

		$this->assertSame($registry->loadString('foo="testloadini1"', 'INI'), $registry, 'The loadString() method should return $this');
		$this->assertSame('testloadini1', $registry->get('foo'), 'The string\'s data should be correctly loaded.');
	}

	/**
	 * @testdox  Two Registry instances can be merged
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testTwoRegistryInstancesCanBeMerged()
	{
		$array1 = [
			'foo' => 'bar',
			'hoo' => 'hum',
			'dum' => [
				'dee' => 'dum'
			]
		];

		$array2 = [
			'foo' => 'soap',
			'dum' => 'huh'
		];

		$registry1 = new Registry($array1);
		$registry2 = new Registry($array2);

		$this->assertSame($registry1->merge($registry2), $registry1, 'The merge() method should return $this');
		$this->assertSame('soap', $registry1->get('foo'), 'The second Registry instance\'s data should be correctly merged into the first.');
	}

	/**
	 * @testdox  A subset of data can be extracted to a new Registry
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testASubsetOfDataCanBeExtractedToANewRegistry()
	{
		$a = new Registry(
			[
				'foo'    => 'bar',
				'subset' => [
					'data1' => 'test1',
					'data2' => 'test2',
					'data3' => [1, 2, 3]
				]
			]
		);

		$b = $a->extract('subset');

		$this->assertInstanceOf(Registry::class, $b, 'The extracted data should be a Registry instance.');
		$this->assertNotSame($a, $b, 'The extracted Registry should be a new Registry instance.');
		$this->assertNull($b->get('foo'), 'The extracted Registry should not contain data that is not part of a subset.');
	}

	/**
	 * @testdox  A Registry can be extracted from null data
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testARegistryCanBeExtractedFromNullData()
	{
		$a = new Registry;
		$b = $a->extract('foo');

		$this->assertInstanceOf(Registry::class, $b, 'A Registry can be extracted from null data.');
		$this->assertNotSame($a, $b, 'Extracting a Registry should always create a new instance.');
	}

	/**
	 * @testdox  The array offset is correctly checked
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testCheckOffsetAsAnArray()
	{
		$instance = new Registry;

		$this->assertEmpty($instance['foo.bar'], 'Checks an offset is empty.');

		$instance->set('foo.bar', 'value');

		$this->assertTrue(isset($instance['foo.bar']), 'Checks a known offset by isset.');
		$this->assertFalse(isset($instance['goo.car']), 'Checks an uknown offset.');
	}

	/**
	 * @testdox  The array offset is correctly retrieved
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testGetOffsetAsAnArray()
	{
		$instance = new Registry(['foo' => ['bar' => 'value']]);

		$this->assertSame('value', $instance['foo.bar'], 'Checks a known offset.');
		$this->assertNull($instance['goo.car'], 'Checks a unknown offset.');
	}

	/**
	 * @testdox  The array offset is correctly set
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testSetOffsetAsAnArray()
	{
		$instance = new Registry;

		$instance['foo.bar'] = 'value';
		$this->assertSame('value', $instance->get('foo.bar'));
	}

	/**
	 * @testdox  The array offset is correctly removed
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testRemoveOffsetAsAnArray()
	{
		$instance = new Registry;
		$instance->set('foo.bar', 'value');

		unset($instance['foo.bar']);
		$this->assertFalse(isset($instance['foo.bar']));
	}

	/**
	 * @testdox  A value is stored to the Registry
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testAValueIsStoredToTheRegistry()
	{
		$a = new Registry;

		$this->assertSame('testsetvalue1', $a->set('foo', 'testsetvalue1'), 'The current value should be returned when assigning a key for the first time.');
		$this->assertSame('testsetvalue2', $a->set('foo', 'testsetvalue2'), 'The new value should be returned when assigning a key multiple times.');
	}

	/**
	 * @testdox  A key is appended to a nested path
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testAKeyIsAppendedToANestedPath()
	{
		$a = new Registry(['foo' => ['var1', 'var2', 'var3']]);
		$a->append('foo', 'var4');

		$this->assertSame('var4', $a->get('foo.3'), 'A key is appended to a nested path.');
	}

	/**
	 * @testdox  A key is removed from the Registry
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testAKeyIsRemovedFromTheRegistry()
	{
		$a = new Registry(['foo' => 'bar']);

		$this->assertSame('bar', $a->remove('foo'), 'When removing a key from the Registry its old value should be returned.');
		$this->assertFalse($a->exists('foo'));
	}

	/**
	 * @testdox  A nested key is removed from the Registry
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testANestedKeyIsRemovedFromTheRegistry()
	{
		$a = new Registry(['nested' => ['foo' => 'bar']]);

		$this->assertSame('bar', $a->remove('nested.foo'), 'When removing a key from the Registry its old value should be returned.');
		$this->assertFalse($a->exists('nested.foo'));
	}

	/**
	 * @testdox  The Registry is unchanged when deleting a non-existing value
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testTheRegistryIsUnchangedWhenDeletingANonExistingValue()
	{
		$a = new Registry(['foo' => 'bar']);

		$this->assertNull($a->remove('goo'));
		$this->assertNull($a->remove('nested.goo'));

		$this->assertEquals($a->toArray(), ['foo' => 'bar']);
	}

	/**
	 * @testdox  The Registry handles mixed array structures correctly
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testMixedArrayStructuresHandledCorrectly()
	{
		$a = new Registry;
		$a->loadArray(
			[
				'assoc' => [
					'foo' => 'bar'
				],
				'unassoc' => [
					'baz', 'baz2', 'baz3'
				],
				'mixed' => [
					'var', 'var2', 'key' => 'var3'
				]
			]
		);

		$a->set('assoc.foo2', 'bar2');
		$this->assertSame('bar2', $a->get('assoc.foo2'));

		$a->set('mixed.key2', 'var4');
		$this->assertSame('var4', $a->get('mixed.key2'));

		$a->set('mixed.2', 'var5');
		$this->assertSame('var5', $a->get('mixed.2'));
		$this->assertSame('var2', $a->get('mixed.1'));

		$a->set('unassoc.3', 'baz4');
		$this->assertSame('baz4', $a->get('unassoc.3'));

		$this->assertTrue(\is_array($a->get('unassoc')), 'Un-associative array should remain after write');
	}

	/**
	 * @testdox  The Registry can be converted to an array
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testTheRegistryCanBeConvertedToAnArray()
	{
		$this->assertSame(
			[
				'foo1' => 'testtoarray1',
				'foo2' => 'testtoarray2',
				'config' => ['foo3' => 'testtoarray3']
			],
			(new Registry(['foo1' => 'testtoarray1', 'foo2' => 'testtoarray2', 'config' => ['foo3' => 'testtoarray3']]))->toArray(),
			'The Registry should be converted to an array.'
		);
	}

	/**
	 * @testdox  The Registry can be converted to an object
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testTheRegistryCanBeConvertedToAnObject()
	{
		$expected = new \stdClass;
		$expected->foo1 = 'testtoobject1';
		$expected->foo2 = 'testtoobject2';
		$expected->config = new \stdClass;
		$expected->config->foo3 = 'testtoobject3';

		$this->assertEquals(
			$expected,
			(new Registry(['foo1' => 'testtoobject1', 'foo2' => 'testtoobject2', 'config' => ['foo3' => 'testtoobject3']]))->toObject(),
			'The Registry should be converted to an object.'
		);
	}

	/**
	 * @testdox  The Registry can be converted to a string
	 *
	 * @covers   Joomla\Registry\Registry
	 * @uses     Joomla\Registry\Factory
	 * @uses     Joomla\Registry\Format\Json
	 */
	public function testTheRegistryCanBeConvertedToAString()
	{
		$a = new Registry(['foo1' => 'testtostring1', 'foo2' => 'testtostring2', 'config' => ['foo3' => 'testtostring3']]);
		$a->set('foo1', 'testtostring1');
		$a->set('foo2', 'testtostring2');
		$a->set('config.foo3', 'testtostring3');

		$this->assertSame(
			'{"foo1":"testtostring1","foo2":"testtostring2","config":{"foo3":"testtostring3"}}',
			trim($a->toString('JSON')),
			'The Registry is converted to a JSON string.'
		);
	}

	/**
	 * @testdox  The Registry can be flattened to an array
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testTheRegistryCanBeFlattenedToAnArray()
	{
		$a = new Registry(['flower' => ['sunflower' => 'light', 'sakura' => 'samurai']]);

		$flattened = $a->flatten();

		$this->assertEquals($flattened['flower.sunflower'], 'light', 'The Registry is flattened to an array.');

		$flattened = $a->flatten('/');

		$this->assertEquals($flattened['flower/sakura'], 'samurai', 'The Registry is flattened to an array with a custom path separator.');
	}

	/**
	 * @testdox  The Registry operates correctly with custom path separators
	 *
	 * @covers   Joomla\Registry\Registry
	 */
	public function testCustomPathSeparatorsCanBeUsed()
	{
		$a = new Registry;
		$a->separator = '\\';
		$a->set('Foo\\Bar', 'test1');
		$a->separator = '/';
		$a->set('Foo/Baz', 'test2');

		$this->assertEquals($a->get('Foo/Bar'), 'test1');
		$this->assertEquals($a->get('Foo/Baz'), 'test2');
	}
}

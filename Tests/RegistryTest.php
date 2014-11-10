<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests;

use Joomla\Registry\Registry;
use Joomla\Test\TestHelper;

/**
 * Test class for Registry.
 *
 * @since  1.0
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test the Joomla\Registry\Registry::__clone method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::__clone
	 * @since   1.0
	 */
	public function test__clone()
	{
		$a = new Registry(array('a' => '123', 'b' => '456'));
		$a->set('foo', 'bar');
		$b = clone $a;

		$this->assertThat(
			serialize($a),
			$this->equalTo(serialize($b))
		);

		$this->assertThat(
			$a,
			$this->logicalNot($this->identicalTo($b)),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Joomla\Registry\Registry::__toString method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::__toString
	 * @since   1.0
	 */
	public function test__toString()
	{
		$object = new \stdClass;
		$a = new Registry($object);
		$a->set('foo', 'bar');

		// __toString only allows for a JSON value.
		$this->assertThat(
			(string) $a,
			$this->equalTo('{"foo":"bar"}'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Joomla\Registry\Registry::jsonSerialize method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::jsonSerialize
	 * @since   1.0
	 */
	public function testJsonSerialize()
	{
		if (version_compare(PHP_VERSION, '5.4.0', '<'))
		{
			$this->markTestSkipped('This test requires PHP 5.4 or newer.');
		}

		$object = new \stdClass;
		$a = new Registry($object);
		$a->set('foo', 'bar');

		// __toString only allows for a JSON value.
		$this->assertThat(
			json_encode($a),
			$this->equalTo('{"foo":"bar"}'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Tests serializing Registry objects.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSerialize()
	{
		$a = new Registry;
		$a->set('foo', 'bar');

		$serialized = serialize($a);
		$b = unserialize($serialized);

		// __toString only allows for a JSON value.
		$this->assertThat(
			$b,
			$this->equalTo($a),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Joomla\Registry\Registry::def method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::def
	 * @since   1.0
	 */
	public function testDef()
	{
		$a = new Registry;

		$this->assertThat(
			$a->def('foo', 'bar'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '. def should return default value'
		);

		$this->assertThat(
			$a->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '. default should now be the current value'
		);
	}

	/**
	 * Tet the Joomla\Registry\Registry::bindData method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::bindData
	 * @since   1.0
	 */
	public function testBindData()
	{
		$a = new Registry;
		$parent = new \stdClass;

		TestHelper::invoke($a, 'bindData', $parent, 'foo');
		$this->assertThat(
			$parent->{0},
			$this->equalTo('foo'),
			'Line: ' . __LINE__ . ' The input value should exist in the parent object.'
		);

		TestHelper::invoke($a, 'bindData', $parent, array('foo' => 'bar', 'nullstring' => null));
		$this->assertThat(
			$parent->{'foo'},
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . ' The input value should exist in the parent object.'
		);
		$this->assertNull(
			$parent->{'nullstring'},
			'Line: ' . __LINE__ . ' A null string should still be set in the constructor.'
		);

		TestHelper::invoke($a, 'bindData', $parent, array('level1' => array('level2' => 'value2')));
		$this->assertThat(
			$parent->{'level1'}->{'level2'},
			$this->equalTo('value2'),
			'Line: ' . __LINE__ . ' The input value should exist in the parent object.'
		);

		TestHelper::invoke($a, 'bindData', $parent, array('intarray' => array(0, 1, 2)));
		$this->assertThat(
			$parent->{'intarray'},
			$this->equalTo(array(0, 1, 2)),
			'Line: ' . __LINE__ . ' The un-associative array should bind natively.'
		);
	}

	/**
	 * Tests the behavior of the \Countable interface implementation
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::count
	 * @since   1.3.0
	 */
	public function testCountable()
	{
		$a = new Registry;
		$a->set('foo1', 'testtoarray1');
		$a->set('foo2', 'testtoarray2');
		$a->set('config.foo3', 'testtoarray3');

		$this->assertEquals(3, count($a), 'count() should correctly count the number of data elements.');
	}

	/**
	 * Test the Joomla\Registry\Registry::exists method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::exists
	 * @since   1.0
	 */
	public function testExists()
	{
		$a = new Registry;
		$a->set('foo', 'bar1');
		$a->set('config.foo', 'bar2');
		$a->set('deep.level.foo', 'bar3');

		$this->assertThat(
			$a->exists('foo'),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' The path should exist, returning true.'
		);

		$this->assertThat(
			$a->exists('config.foo'),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' The path should exist, returning true.'
		);

		$this->assertThat(
			$a->exists('deep.level.foo'),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' The path should exist, returning true.'
		);

		$this->assertThat(
			$a->exists('deep.level.bar'),
			$this->isFalse(),
			'Line: ' . __LINE__ . ' The path should not exist, returning false.'
		);

		$this->assertThat(
			$a->exists('bar.foo'),
			$this->isFalse(),
			'Line: ' . __LINE__ . ' The path should not exist, returning false.'
		);
	}

	/**
	 * Test the Joomla\Registry\Registry::get method
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::get
	 * @since   1.0
	 */
	public function testGet()
	{
		$a = new Registry;
		$a->set('foo', 'bar');
		$this->assertEquals('bar', $a->get('foo'), 'Line: ' . __LINE__ . ' get method should work.');
		$this->assertNull($a->get('xxx.yyy'), 'Line: ' . __LINE__ . ' get should return null when not found.');
	}

	/**
	 * Test the Joomla\Registry\Registry::getInstance method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::getInstance
	 * @since   1.0
	 */
	public function testGetInstance()
	{
		// Test INI format.
		$a = Registry::getInstance('a');
		$b = Registry::getInstance('a');
		$c = Registry::getInstance('c');

		// Check the object type.
		$this->assertInstanceOf(
			'\\Joomla\\Registry\\Registry',
			$a,
			'Line ' . __LINE__ . ' - Object $a should be an instance of Registry.'
		);

		// Check cache handling for same registry id.
		$this->assertThat(
			$a,
			$this->identicalTo($b),
			'Line: ' . __LINE__ . '.'
		);

		// Check cache handling for different registry id.
		$this->assertThat(
			$a,
			$this->logicalNot($this->identicalTo($c)),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Tests the Joomla\Registry\Registry::getIterator method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::getIterator
	 * @since   1.3.0
	 */
	public function testGetIterator()
	{
		$a = new Registry;
		$this->assertInstanceOf('ArrayIterator', $a->getIterator());
	}

	/**
	 * Test the Joomla\Registry\Registry::loadArray method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::loadArray
	 * @since   1.0
	 */
	public function testLoadArray()
	{
		$array = array(
			'foo' => 'bar'
		);
		$registry = new Registry;
		$result = $registry->loadArray($array);

		// Checking result is self that we can chaining
		$this->assertEquals($result, $registry, '$result should be $registry self that support chaining');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Joomla\Registry\Registry::loadArray method with flattened arrays
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::loadArray
	 * @since   1.0
	 */
	public function testLoadFlattenedArray()
	{
		$array = array(
			'foo.bar'  => 1,
			'foo.test' => 2,
			'bar'      => 3
		);
		$registry = new Registry;
		$registry->loadArray($array, true);

		$this->assertThat(
			$registry->get('foo.bar'),
			$this->equalTo(1),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			$registry->get('foo.test'),
			$this->equalTo(2),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			$registry->get('bar'),
			$this->equalTo(3),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Joomla\Registry\Registry::loadFile method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::loadFile
	 * @since   1.0
	 */
	public function testLoadFile()
	{
		$registry = new Registry;

		// Result is always true, no error checking in method.

		// JSON.
		$result = $registry->loadFile(__DIR__ . '/Stubs/jregistry.json');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);

		// INI.
		$result = $registry->loadFile(__DIR__ . '/Stubs/jregistry.ini', '', 'ini');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);

		// INI + section.
		$result = $registry->loadFile(__DIR__ . '/Stubs/jregistry.ini', '', 'ini', array('processSections' => true));

		// Checking result is self that we can chaining
		$this->assertEquals($result, $registry, '$result should be $registry self that support chaining');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('section.foo'),
			$this->equalTo('bar'),
			'Line: ' . __LINE__ . '.'
		);

		// XML and PHP versions do not support stringToObject.
	}

	/**
	 * Test the Joomla\Registry\Registry::loadString() method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::loadString
	 * @since   1.0
	 */
	public function testLoadString()
	{
		$registry = new Registry;
		$result = $registry->loadString('foo="testloadini1"', '', 'INI');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('testloadini1'),
			'Line: ' . __LINE__ . '.'
		);

		$result = $registry->loadString("[section]\nfoo=\"testloadini2\"", '', 'INI');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('testloadini2'),
			'Line: ' . __LINE__ . '.'
		);

		$result = $registry->loadString("[section]\nfoo=\"testloadini3\"", 'testpath', 'INI', array('processSections' => true));

		// Test getting a known value after processing sections.
		$this->assertThat(
			$registry->get('testpath.section.foo'),
			$this->equalTo('testloadini3'),
			'Line: ' . __LINE__ . '.'
		);

		$string = '{"foo":"testloadjson"}';

		$registry = new Registry;
		$result = $registry->loadString($string);

		// Checking result is self that we can chaining
		$this->assertEquals($result, $registry, '$result should be $registry self that support chaining');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('testloadjson'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Joomla\Registry\Registry::loadObject method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::loadObject
	 * @since   1.0
	 */
	public function testLoadObject()
	{
		$object = new \stdClass;
		$object->foo = 'testloadobject';

		$registry = new Registry;
		$result = $registry->loadObject($object);

		// Checking result is self that we can chaining
		$this->assertEquals($result, $registry, '$result should be $registry self that support chaining');

		// Test getting a known value.
		$this->assertThat(
			$registry->get('foo'),
			$this->equalTo('testloadobject'),
			'Line: ' . __LINE__ . '.'
		);

		// Test that loadObject will auto recursive merge
		$registry = new Registry;

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

		$registry->loadObject(json_decode($object1));
		$registry->loadObject(json_decode($object2));

		$this->assertEquals($registry->get('bar.bar2'), 'new bar value 2', 'Line: ' . __LINE__ . '. bar.bar2 shuould be override.');
		$this->assertEquals($registry->get('bar.bar1'), 'bar value 1', 'Line: ' . __LINE__ . '. bar.bar1 should not be overrided.');
	}

	/**
	 * Test the Joomla\Registry\Registry::merge method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::merge
	 * @since   1.0
	 */
	public function testMerge()
	{
		$array1 = array(
			'foo' => 'bar',
			'hoo' => 'hum',
			'dum' => array(
				'dee' => 'dum'
			)
		);

		$array2 = array(
			'foo' => 'soap',
			'dum' => 'huh'
		);
		$registry1 = new Registry;
		$registry1->loadArray($array1);

		$registry2 = new Registry;
		$registry2->loadArray($array2);

		$registry1->merge($registry2);

		// Test getting a known value.
		$this->assertThat(
			$registry1->get('foo'),
			$this->equalTo('soap'),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			$registry1->get('dum'),
			$this->equalTo('huh'),
			'Line: ' . __LINE__ . '.'
		);

		// Test merge with zero and blank value
		$json1 = '{
			"param1":1,
			"param2":"value2"
		}';
		$json2 = '{
			"param1":2,
			"param2":"",
			"param3":0,
			"param4":-1,
			"param5":1
		}';
		$a = new Registry($json1);
		$b = new Registry;
		$b->loadString($json2, 'JSON');
		$result = $a->merge($b);

		// New param with zero value should show in merged registry
		$this->assertEquals(2, $a->get('param1'), '$b value should override $a value');
		$this->assertEquals('value2', $a->get('param2'), '$a value should override blank $b value');
		$this->assertEquals(0, $a->get('param3'), '$b value of 0 should override $a value');
		$this->assertEquals(-1, $a->get('param4'), '$b value of -1 should override $a value');
		$this->assertEquals(1, $a->get('param5'), '$b value of 1 should override $a value');

		// Test recursive merge
		$registry = new Registry;

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

		$registry1->merge($registry2, true);

		$this->assertEquals($registry1->get('bar.bar2'), 'new bar value 2', 'Line: ' . __LINE__ . '. bar.bar2 shuould be override.');
		$this->assertEquals($registry1->get('bar.bar1'), 'bar value 1', 'Line: ' . __LINE__ . '. bar.bar1 should not be overrided.');

		// Chicking we merge a non Registry object will return error.
		$a = new Registry;
		$b = new Registry;

		try
		{
			$a->merge($b);
		}
		catch (Exception $e)
		{
			$this->assertInstanceOf('PHPUnit_Framework_Error', $e, 'Line: ' . __LINE__ . '. Attempt to merge non Registry should return Error');
		}
	}

	/**
	 * Test the Joomla\Registry\Registry::extract method
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::extract
	 * @since   1.2.0
	 */
	public function testExtract()
	{
		$a = new Registry(
			array(
				'foo'    => 'bar',
				'subset' => array(
					'data1' => 'test1',
					'data2' => 'test2',
					'data3' => array(1, 2, 3)
				)
			)
		);

		$b = $a->extract('subset');
		$c = $a->extract('subset.data3');

		$this->assertInstanceOf(
			'\\Joomla\\Registry\\Registry',
			$b,
			'Line ' . __LINE__ . ' - Object $b should be an instance of Registry.'
		);

		$this->assertInstanceOf(
			'\\Joomla\\Registry\\Registry',
			$c,
			'Line ' . __LINE__ . ' - Object $c should be an instance of Registry.'
		);

		$this->assertEquals('test2', $b->get('data2'), 'Test sub-registry path');
	}

	/**
	 * Test the Joomla\Registry\Registry::offsetExists method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::offsetExists
	 * @since   1.0
	 */
	public function testOffsetExists()
	{
		$instance = new Registry;

		$this->assertTrue(empty($instance['foo.bar']));

		$instance->set('foo.bar', 'value');

		$this->assertTrue(isset($instance['foo.bar']), 'Checks a known offset by isset.');
		$this->assertFalse(isset($instance['goo.car']), 'Checks an uknown offset.');
	}

	/**
	 * Test the Joomla\Registry\Registry::offsetGet method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::offsetGet
	 * @since   1.0
	 */
	public function testOffsetGet()
	{
		$instance = new Registry;
		$instance->set('foo.bar', 'value');

		$this->assertEquals('value', $instance['foo.bar'], 'Checks a known offset.');
		$this->assertNull($instance['goo.car'], 'Checks a unknown offset.');
	}

	/**
	 * Test the Joomla\Registry\Registry::offsetSet method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::offsetSet
	 * @since   1.0
	 */
	public function testOffsetSet()
	{
		$instance = new Registry;

		$instance['foo.bar'] = 'value';
		$this->assertEquals('value', $instance->get('foo.bar'), 'Checks the set.');
	}

	/**
	 * Test the Joomla\Registry\Registry::offsetUnset method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::offsetUnset
	 * @since   1.0
	 */
	public function testOffsetUnset()
	{
		$instance = new Registry;
		$instance->set('foo.bar', 'value');

		unset($instance['foo.bar']);
		$this->assertFalse(isset($instance['foo.bar']));
	}

	/**
	 * Test the Joomla\Registry\Registry::set method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::set
	 * @since   1.0
	 */
	public function testSet()
	{
		$a = new Registry;
		$a->set('foo', 'testsetvalue1');
		$a->set('bar/foo', 'testsetvalue3', '/');

		$this->assertThat(
			$a->set('foo', 'testsetvalue2'),
			$this->equalTo('testsetvalue2'),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			$a->set('bar/foo', 'testsetvalue4'),
			$this->equalTo('testsetvalue4'),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Joomla\Registry\Registry::append method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::append
	 * @since   1.0
	 */
	public function testAppend()
	{
		$a = new Registry;
		$a->set('foo', array('var1', 'var2', 'var3'));
		$a->append('foo', 'var4');

		$this->assertThat(
			$a->get('foo.3'),
			$this->equalTo('var4'),
			'Line: ' . __LINE__ . '.'
		);

		$b = $a->get('foo');
		$this->assertTrue(is_array($b));

		$b[] = 'var5';
		$this->assertNull($a->get('foo.4'));
	}

	/**
	 * Test the registry set for unassociative arrays
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testUnassocArrays()
	{
		$a = new Registry;
		$a->loadArray(
			array(
				'assoc' => array(
					'foo' => 'bar'
				),
				'unassoc' => array(
					'baz', 'baz2', 'baz3'
				),
				'mixed' => array(
					'var', 'var2', 'key' => 'var3'
				)
			)
		);

		$a->set('assoc.foo2', 'bar2');
		$this->assertEquals('bar2', $a->get('assoc.foo2'));

		$a->set('mixed.key2', 'var4');
		$this->assertEquals('var4', $a->get('mixed.key2'));

		$a->set('mixed.2', 'var5');
		$this->assertEquals('var5', $a->get('mixed.2'));
		$this->assertEquals('var2', $a->get('mixed.1'));

		$a->set('unassoc.3', 'baz4');
		$this->assertEquals('baz4', $a->get('unassoc.3'));

		$this->assertTrue(is_array($a->get('unassoc')), 'Un-associative array should remain after write');
	}

	/**
	 * Test the Joomla\Registry\Registry::toArray method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::toArray
	 * @since   1.0
	 */
	public function testToArray()
	{
		$a = new Registry;
		$a->set('foo1', 'testtoarray1');
		$a->set('foo2', 'testtoarray2');
		$a->set('config.foo3', 'testtoarray3');

		$expected = array(
			'foo1' => 'testtoarray1',
			'foo2' => 'testtoarray2',
			'config' => array('foo3' => 'testtoarray3')
		);

		$this->assertThat(
			$a->toArray(),
			$this->equalTo($expected),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Joomla\Registry\Registry::toObject method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::toObject
	 * @since   1.0
	 */
	public function testToObject()
	{
		$a = new Registry;
		$a->set('foo1', 'testtoobject1');
		$a->set('foo2', 'testtoobject2');
		$a->set('config.foo3', 'testtoobject3');

		$expected = new \stdClass;
		$expected->foo1 = 'testtoobject1';
		$expected->foo2 = 'testtoobject2';
		$expected->config = new \stdClass;
		$expected->config->foo3 = 'testtoobject3';

		$this->assertThat(
			$a->toObject(),
			$this->equalTo($expected),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test the Joomla\Registry\Registry::toString method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::toString
	 * @since   1.0
	 */
	public function testToString()
	{
		$a = new Registry;
		$a->set('foo1', 'testtostring1');
		$a->set('foo2', 'testtostring2');
		$a->set('config.foo3', 'testtostring3');

		$this->assertThat(
			trim($a->toString('JSON')),
			$this->equalTo(
				'{"foo1":"testtostring1","foo2":"testtostring2","config":{"foo3":"testtostring3"}}'
			),
			'Line: ' . __LINE__ . '.'
		);

		$this->assertThat(
			trim($a->toString('INI')),
			$this->equalTo(
				"foo1=\"testtostring1\"\nfoo2=\"testtostring2\"\n\n[config]\nfoo3=\"testtostring3\""
			),
			'Line: ' . __LINE__ . '.'
		);
	}

	/**
	 * Test flatten.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Registry\Registry::flatten
	 * @since   1.3.0
	 */
	public function testFlatten()
	{
		$a = new Registry;
		$a->set('flower.sunflower', 'light');
		$a->set('flower.sakura', 'samurai');

		$flatted = $a->flatten();

		$this->assertEquals($flatted['flower.sunflower'], 'light');

		$flatted = $a->flatten('/');

		$this->assertEquals($flatted['flower/sakura'], 'samurai');
	}

	/**
	 * Test separator operations
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function testSeparator()
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

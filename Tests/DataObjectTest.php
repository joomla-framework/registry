<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Data\Tests;

use Joomla\Data\DataObject;
use Joomla\Data\Tests\Stubs\Buran;
use Joomla\Data\Tests\Stubs\Capitaliser;
use Joomla\Registry\Registry;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Data\Object class.
 */
class DataObjectTest extends TestCase
{
	/**
	 * @var  DataObject
	 */
	private $instance;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->instance = new DataObject;
	}

	/**
	 * Tests the Joomla\Data\DataObject::object constructor.
	 *
	 * @covers	Joomla\Data\DataObject::__construct
	 */
	public function test__construct()
	{
		$instance = new DataObject(['property1' => 'value1', 'property2' => 5]);

		$this->assertEquals(
			'value1',
			$instance->property1
		);
	}

	/**
	 * Tests the Joomla\Data\DataObject::__get method.
	 *
	 * @covers  Joomla\Data\DataObject::__get
	 */
	public function test__get()
	{
		$this->assertNull(
			$this->instance->foobar,
			'Unknown property should return null.'
		);
	}

	/**
	 * Tests the Joomla\Data\DataObject::__isset method.
	 *
	 * @covers  Joomla\Data\DataObject::__isset
	 */
	public function test__isset()
	{
		$this->assertFalse(isset($this->instance->title), 'Unknown property');

		$this->instance->bind(['title' => true]);

		$this->assertTrue(isset($this->instance->title), 'Property is set.');
	}

	/**
	 * Tests the Joomla\Data\DataObject::__set method where a custom setter is available.
	 *
	 * @covers  Joomla\Data\DataObject::__set
	 */
	public function test__set_setter()
	{
		$instance = new Capitaliser;

		// Set the property and assert that it is the expected value.
		$instance->test_value = 'one';
		$this->assertEquals('ONE', $instance->test_value);

		$instance->bind(['test_value' => 'two']);
		$this->assertEquals('TWO', $instance->test_value);
	}

	/**
	 * Tests the Joomla\Data\DataObject::__unset method.
	 *
	 * @covers  Joomla\Data\DataObject::__unset
	 */
	public function test__unset()
	{
		$this->instance->bind(['title' => true]);

		$this->assertTrue(isset($this->instance->title));

		unset($this->instance->title);

		$this->assertFalse(isset($this->instance->title));
	}

	/**
	 * Tests the Joomla\Data\DataObject::bind method.
	 *
	 * @covers  Joomla\Data\DataObject::bind
	 */
	public function testBind()
	{
		$properties = ['null' => null];

		$this->instance->null = 'notNull';
		$this->instance->bind($properties, false);
		$this->assertSame('notNull', $this->instance->null, 'Checking binding without updating nulls works correctly.');

		$this->instance->bind($properties);
		$this->assertSame(null, $this->instance->null, 'Checking binding with updating nulls works correctly.');
	}

	/**
	 * Tests the Joomla\Data\DataObject::bind method with array input.
	 *
	 * @covers  Joomla\Data\DataObject::bind
	 */
	public function testBind_array()
	{
		$properties = [
			'property_1' => 'value_1',
			'property_2' => '1',
			'property_3' => 1,
			'property_4' => false,
			'property_5' => ['foo'],
		];

		// Bind an array to the object.
		$this->instance->bind($properties);

		// Assert that the values match.
		foreach ($properties as $property => $value)
		{
			$this->assertEquals($value, $this->instance->$property);
		}
	}

	/**
	 * Tests the Joomla\Data\DataObject::bind method with input that is a traverable object.
	 *
	 * @covers  Joomla\Data\DataObject::bind
	 */
	public function testBind_arrayObject()
	{
		$properties = [
			'property_1' => 'value_1',
			'property_2' => '1',
			'property_3' => 1,
			'property_4' => false,
			'property_5' => ['foo'],
		];

		$traversable = new \ArrayObject($properties);

		// Bind an array to the object.
		$this->instance->bind($traversable);

		// Assert that the values match.
		foreach ($properties as $property => $value)
		{
			$this->assertEquals($value, $this->instance->$property);
		}
	}

	/**
	 * Tests the Joomla\Data\DataObject::bind method with object input.
	 *
	 * @covers  Joomla\Data\DataObject::bind
	 */
	public function testBind_object()
	{
		$properties             = new \stdClass;
		$properties->property_1 = 'value_1';
		$properties->property_2 = '1';
		$properties->property_3 = 1;
		$properties->property_4 = false;
		$properties->property_5 = ['foo'];

		// Bind an array to the object.
		$this->instance->bind($properties);

		// Assert that the values match.
		foreach ($properties as $property => $value)
		{
			$this->assertEquals($value, $this->instance->$property);
		}
	}

	/**
	 * Tests the Joomla\Data\DataObject::bind method for an expected exception.
	 *
	 * @covers  Joomla\Data\DataObject::bind
	 */
	public function testBind_exception()
	{
		$this->expectException(\InvalidArgumentException::class);

		$this->instance->bind('foobar');
	}

	/**
	 * Tests the Joomla\Data\DataObject::count method.
	 *
	 * @covers  Joomla\Data\DataObject::count
	 */
	public function testCount()
	{
		// Tests the Joomla\Data\Object::current object is empty.
		$this->assertCount(0, $this->instance);

		// Set a complex property.
		$this->instance->foo = [1 => [2]];
		$this->assertCount(1, $this->instance);

		// Set some more properties.
		$this->instance->bar  = 'bar';
		$this->instance->barz = 'barz';
		$this->assertCount(3, $this->instance);
	}

	/**
	 * Tests the Joomla\Data\DataObject::dump method.
	 *
	 * @covers  Joomla\Data\DataObject::dump
	 */
	public function testDump()
	{
		$dump = $this->instance->dump();

		$this->assertEquals(
			'object',
			\gettype($dump),
			'Dump should return an object.'
		);

		$this->assertEmpty(
			get_object_vars($dump),
			'Empty Object should give an empty dump.'
		);

		$properties = [
			'scalar'   => 'value_1',
			'date'     => new \DateTime('2012-01-01'),
			'registry' => new Registry(['key' => 'value']),
			'Object'   => new DataObject(
				[
					'level2' => new DataObject(
						[
							'level3' => new DataObject(
								[
									'level4' => new DataObject(
										[
											'level5' => 'deep',
										]
									),
								]
							),
						]
					),
				]
			),
		];

		// Bind an array to the object.
		$this->instance->bind($properties);

		// Dump the object (default is 3 levels).
		$dump = $this->instance->dump();

		$this->assertEquals($dump->scalar, 'value_1');
		$this->assertEquals($dump->date, '2012-01-01 00:00:00');
		$this->assertEquals($dump->registry, (object) ['key' => 'value']);
		$this->assertInstanceOf(\stdClass::class, $dump->Object->level2);
		$this->assertInstanceOf(\stdClass::class, $dump->Object->level2->level3);
		$this->assertInstanceOf(DataObject::class, $dump->Object->level2->level3->level4);

		$dump = $this->instance->dump(0);
		$this->assertInstanceOf(\DateTime::class, $dump->date);
		$this->assertInstanceOf(Registry::class, $dump->registry);
		$this->assertInstanceOf(DataObject::class, $dump->Object);

		$dump = $this->instance->dump(1);
		$this->assertEquals($dump->date, '2012-01-01 00:00:00');
		$this->assertEquals($dump->registry, (object) ['key' => 'value']);
		$this->assertInstanceOf(\stdClass::class, $dump->Object);
		$this->assertInstanceOf(DataObject::class, $dump->Object->level2);
	}

	/**
	 * Tests the Joomla\Data\DataObject::getIterator method.
	 *
	 * @covers  Joomla\Data\DataObject::getIterator
	 */
	public function testGetIterator()
	{
		$this->assertInstanceOf(\ArrayIterator::class, $this->instance->getIterator());
	}

	/**
	 * Tests the Joomla\Data\DataObject::getProperty method.
	 *
	 * @covers  Joomla\Data\DataObject::getProperty
	 */
	public function testGetProperty()
	{
		$this->instance->bind(['get_test' => 'get_test_value']);
		$this->assertEquals('get_test_value', $this->instance->get_test);
	}

	/**
	 * Tests the Joomla\Data\DataObject::getProperty method.
	 *
	 * @covers  Joomla\Data\DataObject::getProperty
	 */
	public function testGetProperty_exception()
	{
		$this->expectException(\InvalidArgumentException::class);

		$this->instance->bind(['get_test' => 'get_test_value']);

		// Get the reflection property. This should throw an exception.
		$property = TestHelper::getValue($this->instance, 'get_test');
	}

	/**
	 * Tests the Joomla\Data\DataObject::jsonSerialize method.
	 *
	 * @covers  Joomla\Data\DataObject::jsonSerialize
	 */
	public function testJsonSerialize()
	{
		$this->assertEquals('{}', json_encode($this->instance->jsonSerialize()), 'Empty object.');

		$this->instance->bind(['title' => 'Simple Object']);
		$this->assertEquals('{"title":"Simple Object"}', json_encode($this->instance->jsonSerialize()), 'Simple object.');
	}

	/**
	 * Tests the Joomla\Data\DataObject::setProperty method.
	 *
	 * @covers  Joomla\Data\DataObject::setProperty
	 */
	public function testSetProperty()
	{
		$this->instance->set_test = 'set_test_value';
		$this->assertEquals('set_test_value', $this->instance->set_test);

		$object = new Capitaliser;
		$object->test_value = 'upperCase';

		$this->assertEquals('UPPERCASE', $object->test_value);
	}

	/**
	 * Tests the Joomla\Data\DataObject::setProperty method.
	 *
	 * @covers  Joomla\Data\DataObject::setProperty
	 */
	public function testSetProperty_exception()
	{
		$this->expectException(\InvalidArgumentException::class);

		// Get the reflection property. This should throw an exception.
		$property = TestHelper::getValue($this->instance, 'set_test');
	}

	/**
	 * Test that Joomla\Data\DataObject::setProperty() will not set a property which starts with a null byte.
	 *
	 * @covers  Joomla\Data\DataObject::setProperty
	 * @link    https://www.php.net/manual/en/language.types.array.php#language.types.array.casting
	 */
	public function testSetPropertySkipsPropertyWithNullBytes()
	{
		// Create a property that starts with a null byte.
		$property = "\0foo";

		// Attempt to set the property.
		$this->instance->$property = 'bar';

		// The property should not be set.
		$this->assertNull($this->instance->$property);
	}
}

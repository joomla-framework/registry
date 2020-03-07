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
	 * @testdox  A DataObject can be created
	 *
	 * @covers   Joomla\Data\DataObject
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
	 * @testdox  Data can be retrieved from an object
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function test__get()
	{
		$this->assertNull(
			$this->instance->foobar,
			'Unknown property should return null.'
		);
	}

	/**
	 * @testdox  Data can be checked for presence on an object
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function test__isset()
	{
		$this->assertFalse(isset($this->instance->title), 'Unknown property');

		$this->instance->bind(['title' => true]);

		$this->assertTrue(isset($this->instance->title), 'Property is set.');
	}

	/**
	 * @testdox  Data can be set to an object with a custom setter
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function testCustomPropertySetter()
	{
		$instance = new Capitaliser;

		// Set the property and assert that it is the expected value.
		$instance->test_value = 'one';
		$this->assertEquals('ONE', $instance->test_value);

		$instance->bind(['test_value' => 'two']);
		$this->assertEquals('TWO', $instance->test_value);
	}

	/**
	 * @testdox  Data can be removed from an object
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function test__unset()
	{
		$this->instance->bind(['title' => true]);

		$this->assertTrue(isset($this->instance->title));

		unset($this->instance->title);

		$this->assertFalse(isset($this->instance->title));
	}

	/**
	 * @testdox  The bind method binds an array with null properties
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function testBindWithNullValues()
	{
		$properties = ['null' => null];

		$this->instance->null = 'notNull';
		$this->instance->bind($properties, false);
		$this->assertSame('notNull', $this->instance->null, 'Checking binding without updating nulls works correctly.');

		$this->instance->bind($properties);
		$this->assertSame(null, $this->instance->null, 'Checking binding with updating nulls works correctly.');
	}

	/**
	 * @testdox  The bind method binds an array
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function testBindArray()
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
	 * @testdox  The bind method binds an ArrayObject instance
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function testBindArrayObject()
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
	 * @testdox  The bind method binds an object
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function testBindObject()
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
	 * @testdox  The bind method rejects unsupported data types
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function testBindException()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('The $properties argument must be an array or object, a string was given.');

		$this->instance->bind('foobar');
	}

	/**
	 * @testdox  The object can be counted
	 *
	 * @covers   Joomla\Data\DataObject
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
	 * @testdox  The data object can be dumped
	 *
	 * @covers   Joomla\Data\DataObject
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
	 * @testdox  An iterator for the object can be created
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function testGetIterator()
	{
		$this->assertInstanceOf(\ArrayIterator::class, $this->instance->getIterator());
	}

	/**
	 * @testdox  A property can be read
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function testGetProperty()
	{
		$this->instance->bind(['get_test' => 'get_test_value']);
		$this->assertSame('get_test_value', $this->instance->get_test);
	}

	/**
	 * @testdox  The data object can be JSON encoded
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function testJsonSerialize()
	{
		$this->assertJsonStringEqualsJsonString('{}', json_encode($this->instance->jsonSerialize()));

		$this->instance->bind(['title' => 'Simple Object']);

		$this->assertJsonStringEqualsJsonString('{"title":"Simple Object"}', json_encode($this->instance->jsonSerialize()));
	}

	/**
	 * @testdox  A property can be set
	 *
	 * @covers   Joomla\Data\DataObject
	 */
	public function testSetProperty()
	{
		$this->instance->set_test = 'set_test_value';
		$this->assertSame('set_test_value', $this->instance->set_test);

		$object = new Capitaliser;
		$object->test_value = 'upperCase';

		$this->assertSame('UPPERCASE', $object->test_value);
	}

	/**
	 * @testdox  A property cannot be set which begins with a null byte
	 *
	 * @covers   Joomla\Data\DataObject
	 * @link     https://www.php.net/manual/en/language.types.array.php#language.types.array.casting
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

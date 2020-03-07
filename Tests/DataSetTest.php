<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Data\Tests;

use Joomla\Data\DataObject;
use Joomla\Data\DataSet;
use Joomla\Data\Tests\Stubs\Buran;
use Joomla\Data\Tests\Stubs\Vostok;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Data\DataSet class.
 */
class DataSetTest extends TestCase
{
	/**
	 * An instance of the object to test.
	 *
	 * @var  DataSet
	 */
	private $instance;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->instance = new DataSet(
			[
				new Buran,
				new Vostok(['mission' => 'Vostok 1', 'pilot' => 'Yuri Gagarin']),
			]
		);
	}

	/**
	 * Tests the Joomla\Data\DataSet::__construct method.
	 *
	 * @covers  Joomla\Data\DataSet::__construct
	 */
	public function test__construct()
	{
		$this->assertEmpty(TestHelper::getValue(new DataSet, 'objects'), 'New list should have no objects.');

		$input = [
			'key' => new DataObject(['foo' => 'bar']),
		];
		$new   = new DataSet($input);

		$this->assertEquals($input, TestHelper::getValue($new, 'objects'), 'Check initialised object list.');
	}

	/**
	 * Tests the Joomla\Data\DataSet::__construct method with an array that does not contain Data objects.
	 *
	 * @covers  Joomla\Data\DataSet::__construct
	 */
	public function test__construct_array()
	{
		$this->expectException(\InvalidArgumentException::class);

		new DataSet(array('foo'));
	}

	/**
	 * Tests the Joomla\Data\DataSet::__call method.
	 *
	 * @covers  Joomla\Data\DataSet::__call
	 */
	public function test__call()
	{
		$this->assertEquals(
			[1 => 'go'],
			$this->instance->launch('go')
		);
	}

	/**
	 * Tests the Joomla\Data\DataSet::__get method.
	 *
	 * @covers  Joomla\Data\DataSet::__get
	 */
	public function test__get()
	{
		$this->assertEquals(
			[0 => null, 1 => 'Yuri Gagarin'],
			$this->instance->pilot
		);
	}

	/**
	 * Tests the Joomla\Data\DataSet::__isset method.
	 *
	 * @covers  Joomla\Data\DataSet::__isset
	 */
	public function test__isset()
	{
		$this->assertTrue(isset($this->instance->pilot), 'Property exists.');

		$this->assertFalse(isset($this->instance->duration), 'Unknown property');
	}

	/**
	 * Tests the Joomla\Data\DataSet::__set method.
	 *
	 * @covers  Joomla\Data\DataSet::__set
	 */
	public function test__set()
	{
		$this->instance->successful = 'yes';

		$this->assertEquals(
			[0 => 'yes', 1 => 'YES'],
			$this->instance->successful
		);
	}

	/**
	 * Tests the Joomla\Data\DataSet::__unset method.
	 *
	 * @covers  Joomla\Data\DataSet::__unset
	 */
	public function test__unset()
	{
		unset($this->instance->pilot);

		$this->assertNull($this->instance[1]->pilot);
	}

	/**
	 * Tests the Joomla\Data\DataSet::getObjectsKeys method.
	 *
	 * @covers  Joomla\Data\DataSet::getObjectsKeys
	 * @since   1.2.0
	 */
	public function testGetObjectsKeys()
	{
		$instance = new DataSet(
			[
				'key1' => new DataObject(['foo' => 'var', 'bar' => 'var', 'baz' => 'var']),
				'key2' => new DataObject(['foo' => 'var', 'quz' => 'var', 'baz' => 'var']),
				'key3' => new DataObject(['foo' => 'var', 'bar' => 'var']),
			]
		);

		$this->assertEquals(
			['foo', 'bar', 'baz', 'quz'],
			$instance->getObjectsKeys()
		);

		$this->assertEquals(
			['foo'],
			$instance->getObjectsKeys('common')
		);
	}

	/**
	 * Tests the Joomla\Data\DataSet::toArray method.
	 *
	 * @covers  Joomla\Data\DataSet::toArray
	 */
	public function testToArray()
	{
		$instance = new DataSet(
			[
				'key1' => new DataObject(['date1' => '2014-08-29', 'date2' => '2014-09-16']),
				'key2' => new DataObject(['date1' => '2014-07-06', 'date2' => '2014-08-05']),
				'key3' => new DataObject(['date1' => '2013-12-01', 'date2' => '2014-06-26']),
				'key4' => new DataObject(['date1' => '2013-10-07']),
				'key5' => new DataObject(['date2' => '2010-04-01']),
			]
		);

		$array1  = $instance->toArray(true);
		$expect1 = [
			'key1' => ['date1' => '2014-08-29', 'date2' => '2014-09-16'],
			'key2' => ['date1' => '2014-07-06', 'date2' => '2014-08-05'],
			'key3' => ['date1' => '2013-12-01', 'date2' => '2014-06-26'],
			'key4' => ['date1' => '2013-10-07', 'date2' => null],
			'key5' => ['date1' => null, 'date2' => '2010-04-01'],
		];

		$array2  = $instance->toArray(false, 'date1');
		$expect2 = [
			['2014-08-29'],
			['2014-07-06'],
			['2013-12-01'],
			['2013-10-07'],
			[null],
		];

		$array3  = $instance->toArray(false);
		$expect3 = [
			['2014-08-29', '2014-09-16'],
			['2014-07-06', '2014-08-05'],
			['2013-12-01', '2014-06-26'],
			['2013-10-07', null],
			[null, '2010-04-01'],
		];

		$array4  = $instance->toArray(true, 'date2');
		$expect4 = [
			'key1' => ['date2' => '2014-09-16'],
			'key2' => ['date2' => '2014-08-05'],
			'key3' => ['date2' => '2014-06-26'],
			'key4' => ['date2' => null],
			'key5' => ['date2' => '2010-04-01'],
		];

		$this->assertEquals($expect1, $array1, 'Method should return uniform arrays');
		$this->assertEquals($expect2, $array2);
		$this->assertEquals($expect3, $array3);
		$this->assertEquals($expect4, $array4);
	}

	/**
	 * Tests the Joomla\Data\DataSet::count method.
	 *
	 * @covers  Joomla\Data\DataSet::count
	 */
	public function testCount()
	{
		$this->assertCount(2, $this->instance);
	}

	/**
	 * Tests the Joomla\Data\DataSet::clear method.
	 *
	 * @covers  Joomla\Data\DataSet::clear
	 */
	public function testClear()
	{
		$this->assertGreaterThan(0, count($this->instance), 'Check there are objects set.');
		$this->instance->clear();
		$this->assertCount(0, $this->instance, 'Check the objects were cleared.');
	}

	/**
	 * Tests the Joomla\Data\DataSet::current method.
	 *
	 * @covers  Joomla\Data\DataSet::current
	 */
	public function testCurrent()
	{
		$object = $this->instance[0];

		$this->assertEquals(
			$object,
			$this->instance->current()
		);

		$new = new DataSet(['foo' => new DataObject]);

		$this->assertEquals(
			new DataObject,
			$new->current()
		);
	}

	/**
	 * Tests the Joomla\Data\DataSet::dump method.
	 *
	 * @covers  Joomla\Data\DataSet::dump
	 */
	public function testDump()
	{
		$this->assertEquals(
			[
				new \stdClass,
				(object) [
					'mission' => 'Vostok 1',
					'pilot'   => 'Yuri Gagarin',
				],
			],
			$this->instance->dump()
		);
	}

	/**
	 * Tests the Joomla\Data\DataSet::jsonSerialize method.
	 *
	 * @covers  Joomla\Data\DataSet::jsonSerialize
	 */
	public function testJsonSerialize()
	{
		$objects = [];

		foreach ($this->instance as $object)
		{
			$objects[] = $object;
		}

		$this->assertEquals(
			$objects,
			$this->instance->jsonSerialize()
		);
	}

	/**
	 * Tests the Joomla\Data\DataSet::key method.
	 *
	 * @covers  Joomla\Data\DataSet::key
	 */
	public function testKey()
	{
		$this->assertEquals(0, $this->instance->key());
	}

	/**
	 * Tests the Joomla\Data\DataSet::keys method.
	 *
	 * @covers  Joomla\Data\DataSet::keys
	 */
	public function testKeys()
	{
		$instance         = new DataSet;
		$instance['key1'] = new DataObject;
		$instance['key2'] = new DataObject;

		$this->assertEquals(['key1', 'key2'], $instance->keys());
	}

	/**
	 * Tests the Joomla\Data\DataSet::walk method.
	 *
	 * @covers  Joomla\Data\DataSet::walk
	 * @since   1.2.0
	 */
	public function testWalk()
	{
		$instance         = new DataSet;
		$instance['key1'] = new DataObject(['foo' => 'bar']);
		$instance['key2'] = new DataObject(['foo' => 'qux']);

		$instance->walk(
			function (&$object, $key)
			{
				$object->old = $object->foo;
				$object->foo = 'new-value';
			}
		);

		$this->assertEquals('bar', $instance->old['key1']);
		$this->assertEquals('qux', $instance->old['key2']);
		$this->assertEquals('new-value', $instance->foo['key1']);
		$this->assertEquals('new-value', $instance->foo['key2']);
	}

	/**
	 * Tests the Joomla\Data\DataSet::next method.
	 *
	 * @covers  Joomla\Data\DataSet::next
	 */
	public function testNext()
	{
		$this->instance->next();
		$this->assertEquals(
			1,
			TestHelper::getValue($this->instance, 'current')
		);

		$this->instance->next();
		$this->assertNull(
			TestHelper::getValue($this->instance, 'current')
		);

		TestHelper::setValue($this->instance, 'current', false);
		$this->instance->next();
		$this->assertEquals(
			0,
			TestHelper::getValue($this->instance, 'current')
		);
	}

	/**
	 * Tests the Joomla\Data\DataSet::offsetExists method.
	 *
	 * @covers  Joomla\Data\DataSet::offsetExists
	 */
	public function testOffsetExists()
	{
		$this->assertTrue($this->instance->offsetExists(0));
		$this->assertFalse($this->instance->offsetExists(2));
		$this->assertFalse($this->instance->offsetExists('foo'));
	}

	/**
	 * Tests the Joomla\Data\DataSet::offsetGet method.
	 *
	 * @covers  Joomla\Data\DataSet::offsetGet
	 */
	public function testOffsetGet()
	{
		$this->assertInstanceOf(Buran::class, $this->instance->offsetGet(0));
		$this->assertInstanceOf(Vostok::class, $this->instance->offsetGet(1));
		$this->assertNull($this->instance->offsetGet('foo'));
	}

	/**
	 * Tests the Joomla\Data\DataSet::offsetSet method.
	 *
	 * @covers  Joomla\Data\DataSet::OffsetSet
	 */
	public function testOffsetSet()
	{
		$this->instance->offsetSet(0, new DataObject);
		$objects = TestHelper::getValue($this->instance, 'objects');

		$this->assertEquals(new DataObject, $objects[0], 'Checks explicit use of offsetSet.');

		$this->instance[] = new DataObject;
		$this->assertInstanceOf(DataObject::class, $this->instance[1], 'Checks the array push equivalent with [].');

		$this->instance['foo'] = new DataObject;
		$this->assertInstanceOf(DataObject::class, $this->instance['foo'], 'Checks implicit usage of offsetSet.');
	}

	/**
	 * Tests the Joomla\Data\DataSet::offsetSet method for an expected exception
	 *
	 * @covers  Joomla\Data\DataSet::OffsetSet
	 */
	public function testOffsetSet_exception1()
	{
		$this->expectException(\InvalidArgumentException::class);

		// By implication, this will call offsetSet.
		$this->instance['foo'] = 'bar';
	}

	/**
	 * Tests the Joomla\Data\DataSet::offsetUnset method.
	 *
	 * @covers  Joomla\Data\DataSet::OffsetUnset
	 */
	public function testOffsetUnset()
	{
		TestHelper::setValue($this->instance, 'current', 1);

		$this->instance->offsetUnset(1);
		$objects = TestHelper::getValue($this->instance, 'objects');

		$this->assertFalse(isset($objects[1]));

		$this->instance->offsetUnset(0);
		$objects = TestHelper::getValue($this->instance, 'objects');

		$this->assertFalse(isset($objects[0]));

		// Nonexistent offset
		$this->instance->offsetUnset(-1);
	}

	/**
	 * Tests the Joomla\Data\DataSet::offsetRewind method.
	 *
	 * @covers  Joomla\Data\DataSet::rewind
	 */
	public function testOffsetRewind()
	{
		TestHelper::setValue($this->instance, 'current', 'foo');

		$this->instance->rewind();
		$this->assertEquals(0, $this->instance->key());

		$this->instance->clear();
		$this->assertFalse($this->instance->key());
	}

	/**
	 * Tests the Joomla\Data\DataSet::valid method.
	 *
	 * @covers  Joomla\Data\DataSet::valid
	 */
	public function testValid()
	{
		$this->assertTrue($this->instance->valid());

		TestHelper::setValue($this->instance, 'current', null);

		$this->assertFalse($this->instance->valid());
	}

	/**
	 * Test that Data\DataSet::_initialise method indirectly.
	 *
	 * @covers  Joomla\Data\DataSet::initialise
	 */
	public function testInitialise()
	{
		$this->assertInstanceOf(Buran::class, $this->instance[0]);
		$this->assertInstanceOf(Vostok::class, $this->instance[1]);
	}

	/*
	 * Ancillary tests.
	 */

	/**
	 * Tests using Data\DataSet in a foreach statement.
	 *
	 * @coversNothing  Integration test.
	 */
	public function test_foreach()
	{
		// Test multi-item list.
		$tests = [];

		foreach ($this->instance as $key => $object)
		{
			$tests[] = $object->mission;
		}

		$this->assertEquals([null, 'Vostok 1'], $tests);

		// Tests single item list.
		$this->instance->clear();
		$this->instance['1'] = new DataObject;
		$runs                = 0;

		foreach ($this->instance as $key => $object)
		{
			$runs++;
		}

		$this->assertEquals(1, $runs);

		// Exhaustively testing unsetting within a foreach.
		$this->instance['2'] = new DataObject;
		$this->instance['3'] = new DataObject;
		$this->instance['4'] = new DataObject;
		$this->instance['5'] = new DataObject;

		$runs = 0;

		foreach ($this->instance as $k => $v)
		{
			$runs++;

			if ($k != 3)
			{
				unset($this->instance[$k]);
			}
		}

		$this->assertFalse($this->instance->offsetExists(1), 'Index 1 should have been unset.');
		$this->assertFalse($this->instance->offsetExists(2), 'Index 2 should have been unset.');
		$this->assertTrue($this->instance->offsetExists(3), 'Index 3 should be set.');
		$this->assertFalse($this->instance->offsetExists(4), 'Index 4 should have been unset.');
		$this->assertFalse($this->instance->offsetExists(5), 'Index 5 should have been unset.');
		$this->assertCount(1, $this->instance);
		$this->assertEquals(5, $runs, 'Oops, the foreach ran too many times.');
	}
}

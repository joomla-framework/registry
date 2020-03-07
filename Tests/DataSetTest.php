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
	 * @testdox  A DataSet can be created
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function test__construct()
	{
		$this->assertEmpty(TestHelper::getValue(new DataSet, 'objects'), 'New list should have no objects.');

		$input = [
			'key' => new DataObject(['foo' => 'bar']),
		];

		$this->assertEquals($input, TestHelper::getValue(new DataSet($input), 'objects'), 'Check initialised object list.');
	}

	/**
	 * @testdox  A DataSet only allows DataObjects to be injected
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function test__construct_array()
	{
		$this->expectException(\InvalidArgumentException::class);

		new DataSet(['foo']);
	}

	/**
	 * @testdox  Methods on supporting data objects can be called
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function test__call()
	{
		$this->assertSame(
			[1 => 'go'],
			$this->instance->launch('go')
		);
	}

	/**
	 * @testdox  Data can be retrieved from all objects
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function test__get()
	{
		$this->assertSame(
			[0 => null, 1 => 'Yuri Gagarin'],
			$this->instance->pilot
		);
	}

	/**
	 * @testdox  Data can be checked for presence on an object
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function test__isset()
	{
		$this->assertTrue(isset($this->instance->pilot));
		$this->assertFalse(isset($this->instance->duration));
	}

	/**
	 * @testdox  Data can be set to an object
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function test__set()
	{
		$this->instance->successful = 'yes';

		$this->assertSame(
			[0 => 'yes', 1 => 'YES'],
			$this->instance->successful
		);
	}

	/**
	 * @testdox  Data can be unset from an object
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function test__unset()
	{
		unset($this->instance->pilot);

		$this->assertNull($this->instance[1]->pilot);
	}

	/**
	 * @testdox  The object keys from the dataset can be retrieved
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
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
	 * @testdox  The dataset can be converted to an array
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
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
	 * @testdox  The dataset can be counted
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testCount()
	{
		$this->assertCount(2, $this->instance);
	}

	/**
	 * @testdox  The dataset can be cleared
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testClear()
	{
		$this->assertCount(2, $this->instance);

		$this->instance->clear();

		$this->assertCount(0, $this->instance);
	}

	/**
	 * @testdox  The current object in the iterator can be retrieved
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testCurrent()
	{
		$this->assertSame(
			$this->instance[0],
			$this->instance->current()
		);

		$object = new DataObject;
		$new = new DataSet(['foo' => new DataObject]);

		$this->assertSame(
			$object,
			(new DataSet(['foo' => $object]))->current()
		);
	}

	/**
	 * @testdox  The dataset can be dumped
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
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
	 * @testdox  The dataset can be JSON encoded
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testJsonSerialize()
	{
		$this->assertJson(
			json_encode($this->instance)
		);
	}

	/**
	 * @testdox  The keys for the dataset can be retrieved
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testKeys()
	{
		$instance = new DataSet(
			[
				'key1' => new DataObject,
				'key2' => new DataObject,
			]
		);

		$this->assertEquals(['key1', 'key2'], $instance->keys());
	}

	/**
	 * @testdox  The dataset can be walked over
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testWalk()
	{
		$instance         = new DataSet;
		$instance['key1'] = new DataObject(['foo' => 'bar']);
		$instance['key2'] = new DataObject(['foo' => 'qux']);

		$instance->walk(
			static function (DataObject &$object, $key)
			{
				$object->old = $object->foo;
				$object->foo = 'new-value';
			}
		);

		$this->assertSame('bar', $instance->old['key1']);
		$this->assertSame('qux', $instance->old['key2']);
		$this->assertSame('new-value', $instance->foo['key1']);
		$this->assertSame('new-value', $instance->foo['key2']);
	}

	/**
	 * @testdox  The internal pointer correctly iterates
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testNext()
	{
		foreach ($this->instance as $object)
		{
			$this->assertNotNull($this->instance->key());
		}

		$this->assertNull($this->instance->key());
	}

	/**
	 * @testdox  Checking property presence as an array is supported
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testOffsetExists()
	{
		$this->assertTrue(isset($this->instance[0]));
		$this->assertFalse(isset($this->instance[2]));
		$this->assertFalse(isset($this->instance['foo']));
	}

	/**
	 * @testdox  Retrieving data as an array is supported
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testOffsetGet()
	{
		$this->assertInstanceOf(Buran::class, $this->instance[0]);
		$this->assertInstanceOf(Vostok::class, $this->instance[1]);
		$this->assertNull($this->instance['foo']);
	}

	/**
	 * @testdox  Setting data as an array is supported
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testOffsetSet()
	{
		$this->instance[] = new DataObject;
		$this->assertInstanceOf(DataObject::class, $this->instance[2], 'Checks the array push equivalent with [].');

		$this->instance['foo'] = new DataObject;
		$this->assertInstanceOf(DataObject::class, $this->instance['foo'], 'Checks implicit usage of offsetSet.');
	}

	/**
	 * @testdox  Setting an invalid data type as an array throws an exception
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testOffsetSetInvalidData()
	{
		$this->expectException(\InvalidArgumentException::class);

		// By implication, this will call offsetSet.
		$this->instance['foo'] = 'bar';
	}

	/**
	 * @testdox  Unsetting data as an array is supported
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testOffsetUnset()
	{
		$this->instance['foo'] = new DataObject;

		unset($this->instance['foo']);

		$this->assertFalse(isset($this->instance['foo']));
	}

	/**
	 * @testdox  The internal pointer can be rewound
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
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
	 * @testdox  The internal pointer can be validated
	 *
	 * @covers   Joomla\Data\DataSet
	 * @uses     Joomla\Data\DataObject
	 */
	public function testValid()
	{
		$this->assertTrue($this->instance->valid());

		$this->instance->clear();

		$this->assertFalse($this->instance->valid());
	}

	/**
	 * @testdox  The internal pointer can be validated
	 *
	 * @covers  Joomla\Data\DataSet
	 * @uses    Joomla\Data\DataObject
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
	 * @testdox  The data set can be iterated over
	 *
	 * @coversNothing
	 */
	public function testIteration()
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

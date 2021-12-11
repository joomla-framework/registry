<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Event\Tests;

use Joomla\Event\ListenersPriorityQueue;
use Joomla\Event\Tests\Stubs\EmptyListener;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the ListenersPriorityQueue class.
 */
class ListenersPriorityQueueTest extends TestCase
{
	/**
	 * Object being tested.
	 *
	 * @var  ListenersPriorityQueue
	 */
	private $instance;

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 */
	protected function setUp(): void
	{
		$this->instance = new ListenersPriorityQueue;
	}

	/**
	 * @testdox  A listener can only be added a single time
	 *
	 * @covers   Joomla\Event\ListenersPriorityQueue
	 */
	public function testAddExisting()
	{
		$listener = static function () {};

		$this->assertSame($this->instance, $this->instance->add($listener, 5), 'The add method has a fluent interface');
		$this->assertSame($this->instance, $this->instance->add($listener, 0), 'The add method has a fluent interface');
		$this->assertTrue($this->instance->has($listener));
		$this->assertEquals(5, $this->instance->getPriority($listener));
	}

	/**
	 * @testdox  The priority for a listener can be retrieved
	 *
	 * @covers   Joomla\Event\ListenersPriorityQueue
	 */
	public function testGetPriorityNonExisting()
	{
		$listener = static function () {};

		$this->assertSame($this->instance, $this->instance->add($listener, 0), 'The add method has a fluent interface');
		$this->assertSame(0, $this->instance->getPriority($listener));

		$this->assertFalse(
			$this->instance->getPriority(
				static function () {},
				false
			),
			'If a listener is not registered, the default value should be returned'
		);
	}

	/**
	 * @testdox  A listener can be removed from the queue
	 *
	 * @covers   Joomla\Event\ListenersPriorityQueue
	 */
	public function testAddAndRemove()
	{
		$listener1 = static function () {};

		$listener2 = function () {
			return false;
		};

		$this->assertSame($this->instance, $this->instance->add($listener1, 0), 'The add method has a fluent interface');
		$this->assertSame($this->instance, $this->instance->remove($listener2), 'The remove method has a fluent interface');
		$this->assertTrue($this->instance->has($listener1));
		$this->assertFalse($this->instance->has($listener2));

		$this->assertSame($this->instance, $this->instance->add($listener2, 0), 'The add method has a fluent interface');
		$this->assertTrue($this->instance->has($listener2));

		$this->assertSame($this->instance, $this->instance->remove($listener1), 'The remove method has a fluent interface');
		$this->assertFalse($this->instance->has($listener1));

		$this->assertSame($this->instance, $this->instance->remove($listener2), 'The remove method has a fluent interface');
		$this->assertFalse($this->instance->has($listener2));
	}

	/**
	 * @testdox  All listeners are retrieved from the queue in priority order
	 *
	 * @covers   Joomla\Event\ListenersPriorityQueue
	 */
	public function testGetAll()
	{
		$this->assertEmpty($this->instance->getAll());

		$listener1 = static function () {};

		$listener2 = function () {
			return false;
		};

		$this->assertSame($this->instance, $this->instance->add($listener1, 10), 'The add method has a fluent interface');
		$this->assertSame($this->instance, $this->instance->add($listener2, 3), 'The add method has a fluent interface');

		$listeners = $this->instance->getAll();

		$this->assertSame($listeners[0], $listener1);
		$this->assertSame($listeners[1], $listener2);
	}

	/**
	 * @testdox  The queue can be iterated
	 *
	 * @covers   Joomla\Event\ListenersPriorityQueue
	 */
	public function testGetIterator()
	{
		$listener1 = static function () {};

		$listener2 = function () {
			return false;
		};

		$this->assertSame($this->instance, $this->instance->add($listener1, 10), 'The add method has a fluent interface');
		$this->assertSame($this->instance, $this->instance->add($listener2, 3), 'The add method has a fluent interface');

		$this->assertIsIterable($this->instance);
	}

	/**
	 * @testdox  The queue can be iterated multiple times
	 *
	 * @covers   Joomla\Event\ListenersPriorityQueue
	 */
	public function testGetIteratorMultipleIterations()
	{
		$listener1 = static function () {};

		$listener2 = function () {
			return false;
		};

		$this->assertSame($this->instance, $this->instance->add($listener1, 10), 'The add method has a fluent interface');
		$this->assertSame($this->instance, $this->instance->add($listener2, 3), 'The add method has a fluent interface');

		$this->assertEquals($this->instance->getIterator(), $this->instance->getIterator());
	}

	/**
	 * @testdox  The queue can be counted
	 *
	 * @covers   Joomla\Event\ListenersPriorityQueue
	 */
	public function testCount()
	{
		$listener1 = static function () {};

		$listener2 = function () {
			return false;
		};

		$this->assertSame($this->instance, $this->instance->add($listener1, 10), 'The add method has a fluent interface');
		$this->assertSame($this->instance, $this->instance->add($listener2, 3), 'The add method has a fluent interface');

		$this->assertCount(2, $this->instance);
	}
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Event\Tests;

use Joomla\Event\Event;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Event class.
 */
class EventTest extends TestCase
{
	/**
	 * @testdox  An argument can be added to the event
	 *
	 * @covers   Joomla\Event\Event
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testAddArgument()
	{
		$event = $this->createEventWithoutArguments();

		$this->assertSame($event, $event->addArgument('foo', 'bar'), 'The addArgument method has a fluent interface');
		$this->assertTrue($event->hasArgument('foo'));
	}

	/**
	 * @testdox  When an argument already exists on an event, it should not be overwritten
	 *
	 * @covers   Joomla\Event\Event
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testAddArgumentExisting()
	{
		$event = $this->createEventWithoutArguments();

		$this->assertSame($event, $event->addArgument('foo', 'bar'), 'The addArgument method has a fluent interface');
		$this->assertSame($event, $event->addArgument('foo', 'car'), 'The addArgument method has a fluent interface');
		$this->assertSame('bar', $event->getArgument('foo'));
	}

	/**
	 * @testdox  An argument can be set on the event
	 *
	 * @covers   Joomla\Event\Event
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testSetArgument()
	{
		$event = $this->createEventWithoutArguments();

		$this->assertSame($event, $event->setArgument('foo', 'bar'), 'The setArgument method has a fluent interface');
		$this->assertTrue($event->hasArgument('foo'));
	}

	/**
	 * @testdox  When an argument already exists on an event, it is overwritten
	 *
	 * @covers   Joomla\Event\Event
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testSetArgumentExisting()
	{
		$event = $this->createEventWithoutArguments();

		$this->assertSame($event, $event->setArgument('foo', 'bar'), 'The setArgument method has a fluent interface');
		$this->assertSame($event, $event->setArgument('foo', 'car'), 'The setArgument method has a fluent interface');
		$this->assertSame('car', $event->getArgument('foo'));
	}

	/**
	 * @testdox  An argument can be removed from an event
	 *
	 * @covers   Joomla\Event\Event
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testRemoveArgument()
	{
		$event = $this->createEventWithArguments();

		$this->assertNull($event->removeArgument('non-existing'), 'When removing a non-existing argument, null is returned');
		$this->assertSame('bar', $event->removeArgument('string'), 'When removing an existing argument, the value is returned');
		$this->assertFalse($event->hasArgument('string'));
	}

	/**
	 * @testdox  The arguments can be cleared from an event
	 *
	 * @covers   Joomla\Event\Event
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testClearArguments()
	{
		$event = $this->createEventWithArguments();

		$this->assertNotEmpty($event->clearArguments(), 'The event arguments should be returned when clearing them');
		$this->assertFalse($event->hasArgument('string'));
	}

	/**
	 * @testdox  An event can be stopped
	 *
	 * @covers   Joomla\Event\Event
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testStop()
	{
		$event = $this->createEventWithoutArguments();

		$event->stop();
		$this->assertTrue($event->isStopped());
	}

	/**
	 * @testdox  An argument can be set on the event when accessing the event as an array
	 *
	 * @covers   Joomla\Event\Event
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testOffsetSet()
	{
		$event = $this->createEventWithoutArguments();
		$event['foo'] = 'bar';

		$this->assertTrue($event->hasArgument('foo'));
		$this->assertEquals('bar', $event->getArgument('foo'));
	}

	/**
	 * @testdox  An argument requires a name when setting arguments on the event as an array
	 *
	 * @covers   Joomla\Event\Event
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testOffsetSetException()
	{
		$this->expectException(\InvalidArgumentException::class);

		$this->createEventWithoutArguments()[] = 'bar';
	}

	/**
	 * @testdox  An argument can be removed from an event when accessing the event as an array
	 *
	 * @covers   Joomla\Event\Event
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testOffsetUnset()
	{
		$event = $this->createEventWithArguments();

		unset($event['string']);

		$this->assertFalse($event->hasArgument('string'));
	}

	/**
	 * Creates an event without any arguments
	 *
	 * @return  Event
	 */
	private function createEventWithoutArguments(): Event
	{
		return new Event('test');
	}

	/**
	 * Creates an event with some arguments
	 *
	 * @return  Event
	 */
	private function createEventWithArguments(): Event
	{
		return new Event(
			'test',
			[
				'string' => 'bar',
				'object' => new \stdClass,
				'array'  => [
					'foo'  => 'bar',
					'test' => [
						'foo'  => 'bar',
						'test' => 'test',
					],
				],
			]
		);
	}
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Event\Tests;

use Joomla\Event\EventImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the EventImmutable class.
 */
class EventImmutableTest extends TestCase
{
	/**
	 * @testdox  The constructor cannot be triggered multiple times
	 *
	 * @covers   Joomla\Event\EventImmutable
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testCannotBeConstructedMultipleTimes()
	{
		$this->expectException(\BadMethodCallException::class);

		$this->createEventWithoutArguments()->__construct('foo');
	}

	/**
	 * @testdox  An argument cannot be set on the event after it is instantiated
	 *
	 * @covers   Joomla\Event\EventImmutable
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testOffsetSet()
	{
		$this->expectException(\BadMethodCallException::class);

		$this->createEventWithoutArguments()['foo'] = 'bar';
	}

	/**
	 * @testdox  An argument cannot be removed from the event after it is instantiated
	 *
	 * @covers   Joomla\Event\EventImmutable
	 * @uses     Joomla\Event\AbstractEvent
	 */
	public function testOffsetUnSet()
	{
		$this->expectException(\BadMethodCallException::class);

		unset($this->createEventWithoutArguments()['foo']);
	}

	/**
	 * Creates an event without any arguments
	 *
	 * @return  EventImmutable
	 */
	private function createEventWithoutArguments(): EventImmutable
	{
		return new EventImmutable('test');
	}
}

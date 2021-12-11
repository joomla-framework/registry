<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Event\Tests;

use Joomla\Event\Dispatcher;
use Joomla\Event\Event;
use Joomla\Event\EventInterface;
use Joomla\Event\EventImmutable;
use Joomla\Event\Priority;
use Joomla\Event\Tests\Stubs\FirstListener;
use Joomla\Event\Tests\Stubs\SecondListener;
use Joomla\Event\Tests\Stubs\SomethingListener;
use Joomla\Event\Tests\Stubs\ThirdListener;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Dispatcher class.
 */
class DispatcherTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  Dispatcher
	 */
	private $instance;

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 */
	protected function setUp(): void
	{
		$this->instance = new Dispatcher;
	}

	/**
	 * @testdox  A default event object can be set to the dispatcher for a named event
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 * @uses     Joomla\Event\EventImmutable
	 */
	public function testSetEvent()
	{
		$event = new Event('onTest');
		$this->assertSame($this->instance, $this->instance->setEvent($event), 'The setEvent method has a fluent interface');
		$this->assertTrue($this->instance->hasEvent('onTest'));
		$this->assertSame($event, $this->instance->getEvent('onTest'));

		$immutableEvent = new EventImmutable('onAfterSomething');
		$this->assertSame($this->instance, $this->instance->setEvent($immutableEvent), 'The setEvent method has a fluent interface');
		$this->assertTrue($this->instance->hasEvent('onAfterSomething'));
		$this->assertSame($immutableEvent, $this->instance->getEvent('onAfterSomething'));

		// Setting an existing event will replace the old one.
		$eventCopy = new Event('onTest');
		$this->assertSame($this->instance, $this->instance->setEvent($eventCopy), 'The setEvent method has a fluent interface');
		$this->assertTrue($this->instance->hasEvent('onTest'));
		$this->assertSame($eventCopy, $this->instance->getEvent('onTest'));
	}

	/**
	 * @testdox  A default event object can be added to the dispatcher for a named event if one does not already exist
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 * @uses     Joomla\Event\EventImmutable
	 */
	public function testAddEvent()
	{
		$event = new Event('onTest');
		$this->instance->addEvent($event);
		$this->assertTrue($this->instance->hasEvent('onTest'));
		$this->assertSame($event, $this->instance->getEvent('onTest'));

		$immutableEvent = new EventImmutable('onAfterSomething');
		$this->instance->addEvent($immutableEvent);
		$this->assertTrue($this->instance->hasEvent('onAfterSomething'));
		$this->assertSame($immutableEvent, $this->instance->getEvent('onAfterSomething'));

		// Adding an existing event will have no effect.
		$eventCopy = new Event('onTest');
		$this->instance->addEvent($eventCopy);
		$this->assertTrue($this->instance->hasEvent('onTest'));
		$this->assertSame($event, $this->instance->getEvent('onTest'));
	}

	/**
	 * @testdox  The dispatcher can be checked for a default event object for a named event
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 */
	public function testHasEvent()
	{
		$this->assertFalse($this->instance->hasEvent('onTest'));

		$event = new Event('onTest');
		$this->instance->addEvent($event);
		$this->assertTrue($this->instance->hasEvent($event));
	}

	/**
	 * @testdox  A default event object can be retrieved from the dispatcher for a named event
	 *
	 * @covers   Joomla\Event\Dispatcher
	 */
	public function testGetEventNonExisting()
	{
		$this->assertNull($this->instance->getEvent('non-existing'));
		$this->assertFalse($this->instance->getEvent('non-existing', false));
	}

	/**
	 * @testdox  A default event object can be removed from the dispatcher for a named event
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 */
	public function testRemoveEvent()
	{
		// No exception.
		$this->instance->removeEvent('non-existing');

		$event = new Event('onTest');
		$this->instance->addEvent($event);

		// Remove by passing the instance.
		$this->instance->removeEvent($event);
		$this->assertFalse($this->instance->hasEvent('onTest'));

		$this->instance->addEvent($event);

		// Remove by name.
		$this->instance->removeEvent('onTest');
		$this->assertFalse($this->instance->hasEvent('onTest'));
	}

	/**
	 * @testdox  All known default event objects can be retrieved from the dispatcher
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 */
	public function testGetEvents()
	{
		$this->assertEmpty($this->instance->getEvents());

		$event1 = new Event('onBeforeTest');
		$event2 = new Event('onTest');
		$event3 = new Event('onAfterTest');

		$this->assertSame($this->instance, $this->instance->addEvent($event1), 'The addEvent method has a fluent interface');

		$this->instance->addEvent($event2)
			->addEvent($event3);

		$this->assertSame(
			[
				'onBeforeTest' => $event1,
				'onTest'       => $event2,
				'onAfterTest'  => $event3,
			],
			$this->instance->getEvents()
		);
	}

	/**
	 * @testdox  The default event objects can be cleared from the dispatcher
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 */
	public function testClearEvents()
	{
		$event1 = new Event('onBeforeTest');
		$event2 = new Event('onTest');
		$event3 = new Event('onAfterTest');

		$this->assertSame($this->instance, $this->instance->addEvent($event1), 'The addEvent method has a fluent interface');

		$this->instance->addEvent($event2)
			->addEvent($event3);

		$this->instance->clearEvents();

		$this->assertFalse($this->instance->hasEvent('onBeforeTest'));
		$this->assertFalse($this->instance->hasEvent('onTest'));
		$this->assertFalse($this->instance->hasEvent('onAfterTest'));
		$this->assertEmpty($this->instance->getEvents());
	}

	/**
	 * @testdox  The default event objects can be counted
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 */
	public function testCountEvents()
	{
		$this->assertEquals(0, $this->instance->countEvents());

		$event1 = new Event('onBeforeTest');
		$event2 = new Event('onTest');
		$event3 = new Event('onAfterTest');

		$this->assertSame($this->instance, $this->instance->addEvent($event1), 'The addEvent method has a fluent interface');

		$this->instance->addEvent($event2)
			->addEvent($event3);

		$this->assertEquals(3, $this->instance->countEvents());
	}

	/**
	 * @testdox  Event listeners can be added to the dispatcher
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testAddListener()
	{
		// Add 3 listeners listening to the same events.
		$listener1 = new SomethingListener;
		$listener2 = new SomethingListener;
		$listener3 = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener1, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener1, 'onAfterSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener2, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener2, 'onAfterSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener3, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener3, 'onAfterSomething']);

		$this->assertTrue($this->instance->hasListener([$listener1, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener1, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener1, 'onAfterSomething']));

		$this->assertTrue($this->instance->hasListener([$listener2, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener2, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener2, 'onAfterSomething']));

		$this->assertTrue($this->instance->hasListener([$listener3, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener3, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener3, 'onAfterSomething']));

		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onBeforeSomething', [$listener1, 'onBeforeSomething']));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onSomething', [$listener1, 'onSomething']));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onAfterSomething', [$listener1, 'onAfterSomething']));

		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onBeforeSomething', [$listener2, 'onBeforeSomething']));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onSomething', [$listener2, 'onSomething']));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onAfterSomething', [$listener2, 'onAfterSomething']));

		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onBeforeSomething', [$listener3, 'onBeforeSomething']));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onSomething', [$listener3, 'onSomething']));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onAfterSomething', [$listener3, 'onAfterSomething']));
	}

	/**
	 * @testdox  Event listeners can be added to the dispatcher with specified priorities
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testAddListenerSpecifiedPriorities()
	{
		$listener = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener, 'onBeforeSomething'], Priority::MIN);
		$this->instance->addListener('onSomething', [$listener, 'onSomething'], Priority::ABOVE_NORMAL);
		$this->instance->addListener('onAfterSomething', [$listener, 'onAfterSomething'], Priority::MAX);

		$this->assertTrue($this->instance->hasListener([$listener, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener, 'onAfterSomething']));

		$this->assertEquals(Priority::MIN, $this->instance->getListenerPriority('onBeforeSomething', [$listener, 'onBeforeSomething']));
		$this->assertEquals(Priority::ABOVE_NORMAL, $this->instance->getListenerPriority('onSomething', [$listener, 'onSomething']));
		$this->assertEquals(Priority::MAX, $this->instance->getListenerPriority('onAfterSomething', [$listener, 'onAfterSomething']));
	}

	/**
	 * @testdox  Event listeners can be added to the dispatcher as Closures
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testAddClosureListener()
	{
		$listener = static function (EventInterface $event) {

		};

		$this->instance->addListener('onSomething', $listener, Priority::HIGH);
		$this->instance->addListener('onAfterSomething', $listener, Priority::NORMAL);

		$this->assertTrue($this->instance->hasListener($listener, 'onSomething'));
		$this->assertTrue($this->instance->hasListener($listener, 'onAfterSomething'));

		$this->assertEquals(Priority::HIGH, $this->instance->getListenerPriority('onSomething', $listener));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onAfterSomething', $listener));
	}

	/**
	 * @testdox  The priority for an event listener can be retrieved
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testGetListenerPriority()
	{
		$listener = new SomethingListener;
		$this->instance->addListener('onSomething', [$listener, 'onSomething']);

		$this->assertEquals(
			Priority::NORMAL,
			$this->instance->getListenerPriority(
				'onSomething',
				[$listener, 'onSomething']
			)
		);
	}

	/**
	 * @testdox  The event listeners can be retrieved from the dispatcher
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testGetListeners()
	{
		$this->assertEmpty($this->instance->getListeners('onSomething'));

		// Add 3 listeners listening to the same events.
		$listener1 = new SomethingListener;
		$listener2 = new SomethingListener;
		$listener3 = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener1, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener1, 'onAfterSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener2, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener2, 'onAfterSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener3, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener3, 'onAfterSomething']);

		$allListeners = [
			'onBeforeSomething' => [
				[$listener1, 'onBeforeSomething'],
				[$listener2, 'onBeforeSomething'],
				[$listener3, 'onBeforeSomething'],
			],
			'onSomething'       => [
				[$listener1, 'onSomething'],
				[$listener2, 'onSomething'],
				[$listener3, 'onSomething'],
			],
			'onAfterSomething'  => [
				[$listener1, 'onAfterSomething'],
				[$listener2, 'onAfterSomething'],
				[$listener3, 'onAfterSomething'],
			],
		];

		$onBeforeSomethingListeners = $this->instance->getListeners('onBeforeSomething');

		$this->assertSame($allListeners['onBeforeSomething'], $this->instance->getListeners('onBeforeSomething'));
		$this->assertSame($allListeners['onSomething'], $this->instance->getListeners('onSomething'));
		$this->assertSame($allListeners['onAfterSomething'], $this->instance->getListeners('onAfterSomething'));
		$this->assertSame($allListeners, $this->instance->getListeners());
	}

	/**
	 * @testdox  The dispatcher can be checked to determine if a listener is registered
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testHasListener()
	{
		$listener = new SomethingListener;
		$this->instance->addListener('onSomething', [$listener, 'onSomething']);
		$this->assertTrue($this->instance->hasListener([$listener, 'onSomething'], 'onSomething'));
	}

	/**
	 * @testdox  Event listeners can be removed from the dispatcher
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testRemoveListeners()
	{
		// Add 3 listeners listening to the same events.
		$listener1 = new SomethingListener;
		$listener2 = new SomethingListener;
		$listener3 = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething']);

		// Remove the listener from a specific event.
		$this->instance->removeListener('onBeforeSomething', [$listener1, 'onBeforeSomething']);

		$this->assertFalse($this->instance->hasListener([$listener1, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener2, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener3, 'onBeforeSomething']));
	}

	/**
	 * @testdox  The event listeners can be cleared from the dispatcher
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testClearListeners()
	{
		// Add 3 listeners listening to the same events.
		$listener1 = new SomethingListener;
		$listener2 = new SomethingListener;
		$listener3 = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener1, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener1, 'onAfterSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener2, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener2, 'onAfterSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener3, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener3, 'onAfterSomething']);

		// Test without specified event.
		$this->instance->clearListeners();

		$this->assertFalse($this->instance->hasListener([$listener1, 'onBeforeSomething']));
		$this->assertFalse($this->instance->hasListener([$listener2, 'onSomething']));
		$this->assertFalse($this->instance->hasListener([$listener3, 'onAfterSomething']));

		// Test with an event specified.

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener1, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener1, 'onAfterSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener2, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener2, 'onAfterSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener3, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener3, 'onAfterSomething']);

		$this->instance->clearListeners('onSomething');

		$this->assertTrue($this->instance->hasListener([$listener1, 'onBeforeSomething']));
		$this->assertFalse($this->instance->hasListener([$listener2, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener3, 'onAfterSomething']));

		$this->assertFalse($this->instance->hasListener([$listener1, 'onSomething']));
		$this->assertFalse($this->instance->hasListener([$listener3, 'onSomething']));
	}

	/**
	 * @testdox  Event listeners can be counted
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testCountListeners()
	{
		$this->assertEquals(0, $this->instance->countListeners('onTest'));

		// Add 3 listeners listening to the same events.
		$listener1 = new SomethingListener;
		$listener2 = new SomethingListener;
		$listener3 = new SomethingListener;

		$this->instance->addListener('onBeforeSomething', [$listener1, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener1, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener1, 'onAfterSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener2, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener2, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener2, 'onAfterSomething']);
		$this->instance->addListener('onBeforeSomething', [$listener3, 'onBeforeSomething']);
		$this->instance->addListener('onSomething', [$listener3, 'onSomething']);
		$this->instance->addListener('onAfterSomething', [$listener3, 'onAfterSomething']);

		$this->assertEquals(3, $this->instance->countListeners('onSomething'));
	}

	/**
	 * @testdox  An event can be triggered when there are no listeners
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testTriggerEventNoListeners()
	{
		$this->assertInstanceOf(Event::class, $this->instance->triggerEvent('onTest'));

		$event = new Event('onTest');
		$this->assertSame($event, $this->instance->triggerEvent($event), 'The event is returned after being dispatched');
	}

	/**
	 * @testdox  Event listeners are executed in priority then FIFO order
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testTriggerEventSamePriority()
	{
		$first  = new FirstListener;
		$second = new SecondListener;
		$third  = new ThirdListener;

		$fourth = static function (Event $event) {
			$listeners   = $event->getArgument('listeners');
			$listeners[] = 'fourth';
			$event->setArgument('listeners', $listeners);
		};

		$fifth = static function (Event $event) {
			$listeners   = $event->getArgument('listeners');
			$listeners[] = 'fifth';
			$event->setArgument('listeners', $listeners);
		};

		$this->instance->addListener('onSomething', [$first, 'onSomething']);
		$this->instance->addListener('onSomething', [$second, 'onSomething']);
		$this->instance->addListener('onSomething', [$third, 'onSomething']);
		$this->instance->addListener('onSomething', $fourth, Priority::NORMAL);
		$this->instance->addListener('onSomething', $fifth, Priority::NORMAL);

		// Inspect the event arguments to know the order of the listeners.
		/** @var $event Event */
		$event = $this->instance->triggerEvent('onSomething');

		$listeners = $event->getArgument('listeners');

		$this->assertEquals(
			$listeners,
			['first', 'second', 'third', 'fourth', 'fifth']
		);
	}

	/**
	 * @testdox  Event listeners are executed in priority then FIFO order
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testTriggerEventDifferentPriorities()
	{
		$first  = new FirstListener;
		$second = new SecondListener;
		$third  = new ThirdListener;

		$fourth = static function (Event $event) {
			$listeners   = $event->getArgument('listeners');
			$listeners[] = 'fourth';
			$event->setArgument('listeners', $listeners);
		};

		$fifth = static function (Event $event) {
			$listeners   = $event->getArgument('listeners');
			$listeners[] = 'fifth';
			$event->setArgument('listeners', $listeners);
		};

		$this->instance->addListener('onSomething', $fourth, Priority::BELOW_NORMAL);
		$this->instance->addListener('onSomething', $fifth, Priority::BELOW_NORMAL);
		$this->instance->addListener('onSomething', [$first, 'onSomething'], Priority::HIGH);
		$this->instance->addListener('onSomething', [$second, 'onSomething'], Priority::HIGH);
		$this->instance->addListener('onSomething', [$third, 'onSomething'], Priority::ABOVE_NORMAL);

		// Inspect the event arguments to know the order of the listeners.
		/** @var $event Event */
		$event = $this->instance->triggerEvent('onSomething');

		$listeners = $event->getArgument('listeners');

		$this->assertEquals(
			$listeners,
			['first', 'second', 'third', 'fourth', 'fifth']
		);
	}

	/**
	 * @testdox  Event listeners are not executed after a listener stops propagation
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testTriggerEventStopped()
	{
		$first  = new FirstListener;
		$second = new SecondListener;
		$third  = new ThirdListener;

		$stopper = static function (Event $event) {
			$event->stop();
		};

		$this->instance->addListener('onSomething', [$first, 'onSomething']);
		$this->instance->addListener('onSomething', [$second, 'onSomething']);
		$this->instance->addListener('onSomething', $stopper, Priority::NORMAL);
		$this->instance->addListener('onSomething', [$third, 'onSomething']);

		/** @var $event Event */
		$event = $this->instance->triggerEvent('onSomething');

		$listeners = $event->getArgument('listeners');

		// The third listener was not called because the stopper stopped the event.
		$this->assertEquals(
			$listeners,
			['first', 'second']
		);
	}

	/**
	 * @testdox  An event is triggered using a default event object
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\AbstractEvent
	 * @uses     Joomla\Event\Event
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testTriggerEventRegistered()
	{
		$event = new Event('onSomething');

		$listener = new class
		{
			public $triggered = false;

			public function onSomething(Event $event): void
			{
				$this->triggered = true;
			}
		};


		$this->instance->addEvent(new Event('onSomething'));
		$this->instance->addListener('onSomething', [$listener, 'onSomething']);
		$this->instance->triggerEvent('onSomething');
		$this->assertTrue($listener->triggered);
	}

	/**
	 * @testdox  An event subscriber is registered to the dispatcher
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testAddSubscriber()
	{
		$listener = new SomethingListener;

		// Add our event subscriber
		$this->instance->addSubscriber($listener);

		$this->assertTrue($this->instance->hasListener([$listener, 'onBeforeSomething']));
		$this->assertTrue($this->instance->hasListener([$listener, 'onSomething']));
		$this->assertTrue($this->instance->hasListener([$listener, 'onAfterSomething']));

		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onBeforeSomething', [$listener, 'onBeforeSomething']));
		$this->assertEquals(Priority::NORMAL, $this->instance->getListenerPriority('onSomething', [$listener, 'onSomething']));
		$this->assertEquals(Priority::HIGH, $this->instance->getListenerPriority('onAfterSomething', [$listener, 'onAfterSomething']));
	}

	/**
	 * @testdox  An event subscriber is removed from the dispatcher
	 *
	 * @covers   Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testRemoveSubscriber()
	{
		$listener = new SomethingListener;

		// Add our event subscriber
		$this->instance->addSubscriber($listener);

		// And now remove it
		$this->instance->removeSubscriber($listener);

		$this->assertFalse($this->instance->hasListener([$listener, 'onBeforeSomething']));
		$this->assertFalse($this->instance->hasListener([$listener, 'onSomething']));
		$this->assertFalse($this->instance->hasListener([$listener, 'onAfterSomething']));
	}
}

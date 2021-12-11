<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Event\Tests\Command;

use Joomla\Console\Application;
use Joomla\Event\Command\DebugEventDispatcherCommand;
use Joomla\Event\Dispatcher;
use Joomla\Event\Tests\Stubs\SomethingListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Event\Command\DebugEventDispatcherCommand
 */
class DebugEventDispatcherCommandTest extends TestCase
{
	/**
	 * @testdox  The dispatcher can be described when empty
	 *
	 * @covers   Joomla\Event\Command\DebugEventDispatcherCommand
	 * @uses     Joomla\Event\Dispatcher
	 */
	public function testTheCommandIsExecutedWithAnEmptyDispatcher()
	{
		$dispatcher = new Dispatcher;

		$input  = new ArrayInput(
			[
				'command' => 'debug:event-dispatcher',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new DebugEventDispatcherCommand($dispatcher);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('There are no listeners registered to the event dispatcher.', $screenOutput);
	}

	/**
	 * @testdox  The dispatcher can be described when it contains listeners
	 *
	 * @covers   Joomla\Event\Command\DebugEventDispatcherCommand
	 * @uses     Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testTheCommandIsExecutedWithAConfiguredDispatcher()
	{
		$dispatcher = new Dispatcher;
		$dispatcher->addSubscriber(new SomethingListener);

		$input  = new ArrayInput(
			[
				'command' => 'debug:event-dispatcher',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new DebugEventDispatcherCommand($dispatcher);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();

		$this->assertStringContainsString('Registered Listeners Grouped By Event', $screenOutput);
		$this->assertStringContainsString('#1      Joomla\Event\Tests\Stubs\SomethingListener::onAfterSomething()', $screenOutput);
	}

	/**
	 * @testdox  The dispatcher can be described when it contains listeners for a single event
	 *
	 * @covers   Joomla\Event\Command\DebugEventDispatcherCommand
	 * @uses     Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testTheCommandIsExecutedWithAConfiguredDispatcherForASingleEventWithListeners()
	{
		$dispatcher = new Dispatcher;
		$dispatcher->addSubscriber(new SomethingListener);

		$input  = new ArrayInput(
			[
				'command' => 'debug:event-dispatcher',
				'event'   => 'onAfterSomething',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new DebugEventDispatcherCommand($dispatcher);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();

		$this->assertStringContainsString('Registered Listeners for "onAfterSomething" Event', $screenOutput);
		$this->assertStringContainsString('#1      Joomla\Event\Tests\Stubs\SomethingListener::onAfterSomething()', $screenOutput);
	}

	/**
	 * @testdox  The dispatcher can be described when it contains listeners and no listeners match a single event
	 *
	 * @covers   Joomla\Event\Command\DebugEventDispatcherCommand
	 * @uses     Joomla\Event\Dispatcher
	 * @uses     Joomla\Event\ListenersPriorityQueue
	 */
	public function testTheCommandIsExecutedWithAConfiguredDispatcherForASingleEventWithoutListeners()
	{
		$dispatcher = new Dispatcher;
		$dispatcher->addSubscriber(new SomethingListener);

		$input  = new ArrayInput(
			[
				'command' => 'debug:event-dispatcher',
				'event'   => 'onAfterSomethingElse',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new DebugEventDispatcherCommand($dispatcher);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();

		$this->assertStringContainsString('The event "onAfterSomethingElse" does not have any', $screenOutput);
	}
}

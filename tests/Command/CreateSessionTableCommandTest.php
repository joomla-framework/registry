<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session\Tests\Command;

use Joomla\Console\Application;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Session\Command\CreateSessionTableCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Session\Command\CreateSessionTableCommand
 */
class CreateSessionTableCommandTest extends TestCase
{
	/**
	 * @covers  Joomla\Session\Command\CreateSessionTableCommand
	 * @uses    Joomla\Session\Handler\DatabaseHandler
	 */
	public function testTheDatabaseTableIsCreated()
	{
		$db = $this->createMock(DatabaseInterface::class);
		$db->expects($this->once())
			->method('replacePrefix')
			->with('#__session')
			->willReturn('jos_session');

		$db->expects($this->once())
			->method('getTableList')
			->willReturn([]);

		$db->expects($this->once())
			->method('getName')
			->willReturn('mysql');

		$db->expects($this->once())
			->method('setQuery')
			->willReturnSelf();

		$db->expects($this->once())
			->method('execute')
			->willReturn(true);

		$input  = new ArrayInput(
			[
				'command' => 'session:create-table',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new CreateSessionTableCommand($db);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The session table has been created.', $screenOutput);
	}

	/**
	 * @covers  Joomla\Session\Command\CreateSessionTableCommand
	 * @uses    Joomla\Session\Handler\DatabaseHandler
	 */
	public function testTheDatabaseTableIsNotCreatedWhenItAlreadyExists()
	{
		$db = $this->createMock(DatabaseInterface::class);
		$db->expects($this->once())
			->method('replacePrefix')
			->with('#__session')
			->willReturn('jos_session');

		$db->expects($this->once())
			->method('getTableList')
			->willReturn(['jos_session']);

		$db->expects($this->never())
			->method('execute');

		$input  = new ArrayInput(
			[
				'command' => 'session:create-table',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new CreateSessionTableCommand($db);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The session table already exists.', $screenOutput);
	}

	/**
	 * @covers  Joomla\Session\Command\CreateSessionTableCommand
	 * @uses    Joomla\Session\Handler\DatabaseHandler
	 */
	public function testTheDatabaseTableIsNotCreatedWhenTheDatabaseDriverIsNotSupported()
	{
		$db = $this->createMock(DatabaseInterface::class);
		$db->expects($this->once())
			->method('replacePrefix')
			->with('#__session')
			->willReturn('jos_session');

		$db->expects($this->once())
			->method('getTableList')
			->willReturn([]);

		$db->expects($this->exactly(2))
			->method('getName')
			->willReturn('mongodb');

		$db->expects($this->never())
			->method('execute');

		$input  = new ArrayInput(
			[
				'command' => 'session:create-table',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new CreateSessionTableCommand($db);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The mongodb database driver is not supported.', $screenOutput);
	}

	/**
	 * @covers  Joomla\Session\Command\CreateSessionTableCommand
	 * @uses    Joomla\Session\Handler\DatabaseHandler
	 */
	public function testTheDatabaseTableIsNotCreatedWhenTheDatabaseDriverThrowsAnError()
	{
		$db = $this->createMock(DatabaseInterface::class);
		$db->expects($this->once())
			->method('replacePrefix')
			->with('#__session')
			->willReturn('jos_session');

		$db->expects($this->once())
			->method('getTableList')
			->willReturn([]);

		$db->expects($this->once())
			->method('getName')
			->willReturn('mysql');

		$db->expects($this->once())
			->method('setQuery')
			->willReturnSelf();

		$db->expects($this->once())
			->method('execute')
			->willThrowException(new ExecutionFailureException('CREATE TABLE #__session', 'Test failure'));

		$input  = new ArrayInput(
			[
				'command' => 'session:create-table',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new CreateSessionTableCommand($db);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The session table could not be created:', $screenOutput);
	}
}

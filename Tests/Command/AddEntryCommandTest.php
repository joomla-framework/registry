<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Tests\Command;

use Joomla\Console\Application;
use Joomla\Keychain\Command\AddEntryCommand;
use Joomla\Keychain\Tests\KeychainTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Keychain\Command\AddEntryCommand
 */
class AddEntryCommandTest extends KeychainTestCase
{
	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		parent::setUp();
	}

	/**
	 * @testdox  An entry is added to the keychain
	 *
	 * @covers   Joomla\Keychain\Command\AddEntryCommand
	 * @uses     Joomla\Keychain\Command\AbstractKeychainCommand
	 * @uses     Joomla\Keychain\Keychain
	 */
	public function testAKeyIsAddedToAKeychain()
	{
		file_put_contents($this->tmpFile, json_encode((object) []));

		$this->crypt->expects($this->once())
			->method('decrypt')
			->willReturnArgument(0);

		$this->crypt->expects($this->once())
			->method('encrypt')
			->willReturnArgument(0);

		$input  = new ArrayInput(
			[
				'command'     => 'keychain:add-entry',
				'filename'    => $this->tmpFile,
				'entry-name'  => 'foo',
				'entry-value' => 'bar',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new AddEntryCommand($this->crypt);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The entry was added to the keychain.', $screenOutput);
	}

	/**
	 * @testdox  An entry is not added to the keychain when the key already exists
	 *
	 * @covers   Joomla\Keychain\Command\AddEntryCommand
	 * @uses     Joomla\Keychain\Command\AbstractKeychainCommand
	 * @uses     Joomla\Keychain\Keychain
	 */
	public function testAKeyIsNotAddedToAKeychainWhenItAlreadyExists()
	{
		file_put_contents($this->tmpFile, json_encode((object) ['foo' => 'bar']));

		$this->crypt->expects($this->once())
			->method('decrypt')
			->willReturnArgument(0);

		$this->crypt->expects($this->never())
			->method('encrypt');

		$input  = new ArrayInput(
			[
				'command'     => 'keychain:add-entry',
				'filename'    => $this->tmpFile,
				'entry-name'  => 'foo',
				'entry-value' => 'bar',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new AddEntryCommand($this->crypt);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('An entry already exists', $screenOutput);
	}

	/**
	 * @testdox  An entry is not added to the keychain when saving the file fails
	 *
	 * @covers   Joomla\Keychain\Command\AddEntryCommand
	 * @uses     Joomla\Keychain\Command\AbstractKeychainCommand
	 * @uses     Joomla\Keychain\Keychain
	 */
	public function testAKeyIsNotAddedToAKeychainWhenSavingTheUpdatedKeychainFails()
	{
		file_put_contents($this->tmpFile, json_encode((object) []));

		$this->crypt->expects($this->once())
			->method('decrypt')
			->willReturnArgument(0);

		$this->crypt->expects($this->once())
			->method('encrypt')
			->willReturn(null);

		$input  = new ArrayInput(
			[
				'command'     => 'keychain:add-entry',
				'filename'    => $this->tmpFile,
				'entry-name'  => 'foo',
				'entry-value' => 'bar',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new AddEntryCommand($this->crypt);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The entry was not added to the keychain.', $screenOutput);
	}
}

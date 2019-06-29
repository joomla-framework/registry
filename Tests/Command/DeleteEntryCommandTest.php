<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Tests\Command;

use Joomla\Console\Application;
use Joomla\Keychain\Command\DeleteEntryCommand;
use Joomla\Keychain\Tests\KeychainTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Keychain\Command\DeleteEntryCommand
 */
class DeleteEntryCommandTest extends KeychainTestCase
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

	public function testAKeyIsDeletedFromAKeychain()
	{
		file_put_contents($this->tmpFile, json_encode((object) ['foo' => 'bar']));

		$this->crypt->expects($this->once())
			->method('decrypt')
			->willReturnArgument(0);

		$this->crypt->expects($this->once())
			->method('encrypt')
			->willReturnArgument(0);

		$input  = new ArrayInput(
			[
				'command'    => 'keychain:delete-entry',
				'filename'   => $this->tmpFile,
				'entry-name' => 'foo',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new DeleteEntryCommand($this->crypt);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The entry was removed from the keychain.', $screenOutput);
	}

	public function testTheCommandDoesNotFailIfTryingToDeleteANonExistingKeyFromTheKeychain()
	{
		file_put_contents($this->tmpFile, json_encode((object) []));

		$this->crypt->expects($this->once())
			->method('decrypt')
			->willReturnArgument(0);

		$this->crypt->expects($this->never())
			->method('encrypt');

		$input  = new ArrayInput(
			[
				'command'    => 'keychain:delete-entry',
				'filename'   => $this->tmpFile,
				'entry-name' => 'foo',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new DeleteEntryCommand($this->crypt);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('There is no entry in the keychain', $screenOutput);
	}

	public function testAKeyIsNotDeletedFromAKeychainWhenSavingTheUpdatedKeychainFails()
	{
		file_put_contents($this->tmpFile, json_encode((object) ['foo' => 'bar']));

		$this->crypt->expects($this->once())
			->method('decrypt')
			->willReturnArgument(0);

		$this->crypt->expects($this->once())
			->method('encrypt')
			->willReturn(null);

		$input  = new ArrayInput(
			[
				'command'    => 'keychain:delete-entry',
				'filename'   => $this->tmpFile,
				'entry-name' => 'foo',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new DeleteEntryCommand($this->crypt);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The entry was not removed from the keychain.', $screenOutput);
	}
}

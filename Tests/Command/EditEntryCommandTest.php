<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Tests\Command;

use Joomla\Console\Application;
use Joomla\Keychain\Command\EditEntryCommand;
use Joomla\Keychain\Tests\KeychainTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Keychain\Command\EditEntryCommand
 */
class EditEntryCommandTest extends KeychainTestCase
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

	public function testAKeyIsNotEditedInAKeychain()
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
				'command'     => 'keychain:edit-entry',
				'filename'    => $this->tmpFile,
				'entry-name'  => 'foo',
				'entry-value' => 'car',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new EditEntryCommand($this->crypt);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The entry was edited in the keychain.', $screenOutput);
	}

	public function testAKeyIsNotEditedInAKeychainWhenSavingTheUpdatedKeychainFails()
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
				'command'     => 'keychain:edit-entry',
				'filename'    => $this->tmpFile,
				'entry-name'  => 'foo',
				'entry-value' => 'car',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new EditEntryCommand($this->crypt);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The entry was not edited in the keychain.', $screenOutput);
	}
}

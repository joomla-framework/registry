<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Tests\Command;

use Joomla\Console\Application;
use Joomla\Keychain\Command\AbstractKeychainCommand;
use Joomla\Keychain\Tests\KeychainTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test class for \Joomla\Keychain\Command\AbstractKeychainCommand
 */
class AbstractKeychainCommandTest extends KeychainTestCase
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
	 * @testdox  The keychain is initialised before a command is executed
	 *
	 * @covers   Joomla\Keychain\Command\AbstractKeychainCommand
	 * @uses     Joomla\Keychain\Keychain
	 */
	public function testTheKeychainIsInitialisedBeforeACommandIsExecuted()
	{
		file_put_contents($this->tmpFile, json_encode((object) []));

		$this->crypt->expects($this->once())
			->method('decrypt')
			->willReturnArgument(0);

		/** @var InputInterface|MockObject $input */
		$input = $this->createMock(InputInterface::class);
		$input->expects($this->once())
			->method('getArgument')
			->with('filename')
			->willReturn($this->tmpFile);

		/** @var OutputInterface|MockObject $output */
		$output = $this->createMock(OutputInterface::class);

		$application = new Application($input, $output);

		$command = new class($this->crypt) extends AbstractKeychainCommand
		{
			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}
		};

		$command->execute($input, $output);
	}

	/**
	 * @testdox  The keychain is not initialised when the file does not exist
	 *
	 * @covers   Joomla\Keychain\Command\AbstractKeychainCommand
	 * @uses     Joomla\Keychain\Keychain
	 */
	public function testTheKeychainIsNotInitialisedWhenTheFileDoesNotExist()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage(sprintf('There is no readable file at `%s`.', $this->tmpFile));

		$this->crypt->expects($this->never())
			->method('decrypt');

		/** @var InputInterface|MockObject $input */
		$input = $this->createMock(InputInterface::class);
		$input->expects($this->once())
			->method('getArgument')
			->with('filename')
			->willReturn($this->tmpFile);

		/** @var OutputInterface|MockObject $output */
		$output = $this->createMock(OutputInterface::class);

		$application = new Application($input, $output);

		$command = new class($this->crypt) extends AbstractKeychainCommand
		{
			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}
		};

		$command->execute($input, $output);
	}

	/**
	 * @testdox  The keychain is saved
	 *
	 * @covers   Joomla\Keychain\Command\AbstractKeychainCommand
	 * @uses     Joomla\Keychain\Keychain
	 */
	public function testTheKeychainIsSaved()
	{
		file_put_contents($this->tmpFile, json_encode((object) []));

		$this->crypt->expects($this->once())
			->method('decrypt')
			->willReturnArgument(0);

		$this->crypt->expects($this->once())
			->method('encrypt')
			->willReturnArgument(0);

		/** @var InputInterface|MockObject $input */
		$input = $this->createMock(InputInterface::class);
		$input->expects($this->once())
			->method('getArgument')
			->with('filename')
			->willReturn($this->tmpFile);

		/** @var OutputInterface|MockObject $output */
		$output = $this->createMock(OutputInterface::class);

		$application = new Application($input, $output);

		$command = new class($this->crypt) extends AbstractKeychainCommand
		{
			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				$this->saveKeychain();

				return 0;
			}
		};

		$command->execute($input, $output);
	}

	/**
	 * @testdox  The keychain is not saved when the file is not writable
	 *
	 * @covers   Joomla\Keychain\Command\AbstractKeychainCommand
	 * @uses     Joomla\Keychain\Keychain
	 */
	public function testTheKeychainIsNotSavedWhenTheFileIsNotWritable()
	{
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage(sprintf('Cannot write the keychain to `%s` as the path is not writable.', $this->tmpFile));

		$this->crypt->expects($this->never())
			->method('decrypt');

		$this->crypt->expects($this->never())
			->method('encrypt');

		/** @var InputInterface|MockObject $input */
		$input = $this->createMock(InputInterface::class);
		$input->expects($this->once())
			->method('getArgument')
			->with('filename')
			->willReturn($this->tmpFile);

		/** @var OutputInterface|MockObject $output */
		$output = $this->createMock(OutputInterface::class);

		$application = new Application($input, $output);

		$command = new class($this->crypt) extends AbstractKeychainCommand
		{
			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				$this->saveKeychain();

				return 0;
			}

			protected function initialiseKeychain(InputInterface $input): void
			{
				$this->filename = $input->getArgument('filename');
			}
		};

		$command->execute($input, $output);
	}
}

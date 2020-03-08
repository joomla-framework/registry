<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Tests\Command;

use Joomla\Console\Application;
use Joomla\Keychain\Command\ListEntriesCommand;
use Joomla\Keychain\Tests\KeychainTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Keychain\Command\ListEntriesCommand
 */
class ListEntriesCommandTest extends KeychainTestCase
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
	 * @testdox  The list of keys in the keychain can be listed
	 *
	 * @covers   Joomla\Keychain\Command\ListEntriesCommand
	 * @uses     Joomla\Keychain\Command\AbstractKeychainCommand
	 * @uses     Joomla\Keychain\Keychain
	 */
	public function testTheListOfKeysInTheKeychainArePrinted()
	{
		file_put_contents($this->tmpFile, json_encode((object) ['foo' => 'bar']));

		$this->crypt->expects($this->once())
			->method('decrypt')
			->willReturnArgument(0);

		$input  = new ArrayInput(
			[
				'command'  => 'keychain:list-entries',
				'filename' => $this->tmpFile,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ListEntriesCommand($this->crypt);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$expected = <<<EOF
 ----- 
  Key  
 ----- 
  foo  
 -----
EOF;

		$this->assertStringContainsString($expected, $screenOutput);
	}

	/**
	 * @testdox  The list of keys in the keychain and their values can be listed
	 *
	 * @covers   Joomla\Keychain\Command\ListEntriesCommand
	 * @uses     Joomla\Keychain\Command\AbstractKeychainCommand
	 * @uses     Joomla\Keychain\Keychain
	 */
	public function testTheListOfKeysAndValuesInTheKeychainArePrinted()
	{
		file_put_contents($this->tmpFile, json_encode((object) ['foo' => 'bar']));

		$this->crypt->expects($this->once())
			->method('decrypt')
			->willReturnArgument(0);

		$input  = new ArrayInput(
			[
				'command'        => 'keychain:list-entries',
				'filename'       => $this->tmpFile,
				'--print-values' => true,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ListEntriesCommand($this->crypt);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();

		$expected = <<<EOF
 ----- ------- 
  Key   Value  
 ----- ------- 
  foo   bar    
 ----- -------
EOF;

		$this->assertStringContainsString($expected, $screenOutput);
	}
}

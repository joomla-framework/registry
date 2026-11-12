<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Command;

use Joomla\Console\Application;
use Joomla\Registry\Command\ReadEntryCommand;
use Joomla\Registry\Tests\KeychainTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Registry\Command\ReadEntryCommand
 */
class ReadEntryCommandTest extends KeychainTestCase
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
     * @testdox  The value for a key in a keychain can be listed
     *
     * @covers   Joomla\Registry\Command\ReadEntryCommand
     * @uses     Joomla\Registry\Command\AbstractKeychainCommand
     * @uses     Joomla\Registry\Keychain
     */
    public function testTheValueOfAKeyInTheKeychainIsPrinted()
    {
        file_put_contents($this->tmpFile, json_encode((object) ['foo' => 'bar']));

        $this->crypt->expects($this->once())
            ->method('decrypt')
            ->willReturnArgument(0);

        $input  = new ArrayInput(
            [
                'command'    => 'keychain:read-entry',
                'filename'   => $this->tmpFile,
                'entry-name' => 'foo',
            ]
        );
        $output = new BufferedOutput();

        $application = new Application($input, $output);

        $command = new ReadEntryCommand($this->crypt);
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

        $this->assertStringContainsString(
            $this->normaliseLineEndings($expected),
            $this->normaliseLineEndings($screenOutput)
        );
    }

    /**
     * @testdox  A message is shown when attempting to read a key from the keychain that does not exist
     *
     * @covers   Joomla\Registry\Command\ReadEntryCommand
     * @uses     Joomla\Registry\Command\AbstractKeychainCommand
     * @uses     Joomla\Registry\Keychain
     */
    public function testTheValueOfAKeyInTheKeychainIsNotPrintedWhenItDoesNotExist()
    {
        file_put_contents($this->tmpFile, json_encode((object) []));

        $this->crypt->expects($this->once())
            ->method('decrypt')
            ->willReturnArgument(0);

        $input  = new ArrayInput(
            [
                'command'    => 'keychain:read-entry',
                'filename'   => $this->tmpFile,
                'entry-name' => 'foo',
            ]
        );
        $output = new BufferedOutput();

        $application = new Application($input, $output);

        $command = new ReadEntryCommand($this->crypt);
        $command->setApplication($application);

        $this->assertSame(1, $command->execute($input, $output));

        $screenOutput = $output->fetch();

        $this->assertStringContainsString('There is no entry in the keychain', $screenOutput);
    }
    /**
     * Normalise line endings so assertions do not depend on the host platform.
     *
     * @param   string  $value  The value to normalise.
     *
     * @return  string
     */
    private function normaliseLineEndings(string $value): string
    {
        return str_replace(["\r\n", "\r"], "\n", $value);
    }
}
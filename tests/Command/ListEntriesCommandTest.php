<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Command;

use Joomla\Console\Application;
use Joomla\Registry\Command\ListEntriesCommand;
use Joomla\Registry\Tests\KeychainTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Registry\Command\ListEntriesCommand
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
     * @covers   Joomla\Registry\Command\ListEntriesCommand
     * @uses     Joomla\Registry\Command\AbstractKeychainCommand
     * @uses     Joomla\Registry\Keychain
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
        $output = new BufferedOutput();

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

        $this->assertStringContainsString(
            $this->normaliseLineEndings($expected),
            $this->normaliseLineEndings($screenOutput)
        );
    }

    /**
     * @testdox  The list of keys in the keychain and their values can be listed
     *
     * @covers   Joomla\Registry\Command\ListEntriesCommand
     * @uses     Joomla\Registry\Command\AbstractKeychainCommand
     * @uses     Joomla\Registry\Keychain
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
        $output = new BufferedOutput();

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

        $this->assertStringContainsString(
            $this->normaliseLineEndings($expected),
            $this->normaliseLineEndings($screenOutput)
        );
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
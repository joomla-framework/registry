<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Command;

use Joomla\Console\Application;
use Joomla\Registry\Command\EditEntryCommand;
use Joomla\Registry\Tests\KeychainTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Registry\Command\EditEntryCommand
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

    /**
     * @testdox  An entry is edited in the keychain
     *
     * @covers   Joomla\Registry\Command\EditEntryCommand
     * @uses     Joomla\Registry\Command\AbstractKeychainCommand
     * @uses     Joomla\Registry\Keychain
     */
    public function testAKeyIsEditedInAKeychain()
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
        $output = new BufferedOutput();

        $application = new Application($input, $output);

        $command = new EditEntryCommand($this->crypt);
        $command->setApplication($application);

        $this->assertSame(0, $command->execute($input, $output));

        $screenOutput = $output->fetch();
        $this->assertStringContainsString('The entry was edited in the keychain.', $screenOutput);
    }

    /**
     * @testdox  An entry is not edited in the keychain when saving the file fails
     *
     * @covers   Joomla\Registry\Command\EditEntryCommand
     * @uses     Joomla\Registry\Command\AbstractKeychainCommand
     * @uses     Joomla\Registry\Keychain
     */
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
        $output = new BufferedOutput();

        $application = new Application($input, $output);

        $command = new EditEntryCommand($this->crypt);
        $command->setApplication($application);

        $this->assertSame(1, $command->execute($input, $output));

        $screenOutput = $output->fetch();
        $this->assertStringContainsString('The entry was not edited in the keychain.', $screenOutput);
    }
}

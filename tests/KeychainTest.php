<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests;

use Joomla\Registry\Keychain;

/**
 * Test class for \Joomla\Registry\Keychain.
 */
class KeychainTest extends KeychainTestCase
{
    /**
     * The Keychain for testing
     *
     * @var  Keychain
     */
    private $keychain;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->keychain = new Keychain($this->crypt);
    }

    /**
     * @testdox  A keychain can be loaded from a file
     *
     * @covers   Joomla\Registry\Keychain
     */
    public function testAKeychainIsLoadedFromAFile()
    {
        $this->crypt->expects($this->once())
            ->method('decrypt')
            ->willReturnArgument(0);

        $this->assertSame(
            $this->keychain,
            $this->keychain->loadKeychain(__DIR__ . '/data/keychain.json'),
            'When a file is loaded into the keychain the current instance is returned'
        );
    }

    /**
     * @testdox  The keychain fails when trying to load a non-existing file
     *
     * @covers   Joomla\Registry\Keychain
     */
    public function testAKeychainCannotBeLoadedFromANonExistingFile()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Attempting to load non-existent keychain file');

        $this->crypt->expects($this->never())
            ->method('decrypt');

        $this->keychain->loadKeychain(__DIR__ . '/data/does-not-exist.json');
    }

    /**
     * @testdox  A keychain can be saved to a file
     *
     * @covers   Joomla\Registry\Keychain
     */
    public function testAKeychainIsSavedToAFile()
    {
        $this->crypt->expects($this->once())
            ->method('encrypt')
            ->willReturnArgument(0);

        $this->assertNotFalse(
            $this->keychain->saveKeychain($this->tmpFile),
            'A keychain should be saved to the filesystem successfully'
        );
    }

    /**
     * @testdox  A keychain cannot be saved to a file when the file argument is empty
     *
     * @covers   Joomla\Registry\Keychain
     */
    public function testAKeychainCannotBeSavedToTheFilesystemIfAnEmptyPathIsGiven()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('A keychain file must be specified');

        $this->crypt->expects($this->never())
            ->method('encrypt');

        $this->keychain->saveKeychain('');
    }
}

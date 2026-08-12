<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests;

use Joomla\Crypt\Crypt;
use Joomla\Registry\Keychain;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Base test case for Keychain tests
 */
abstract class KeychainTestCase extends TestCase
{
    /**
     * The mock Crypt object
     *
     * @var  Crypt|MockObject
     */
    protected $crypt;

    /**
     * The temporary file used for validating a successful save
     *
     * @var  string
     */
    protected $tmpFile;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tmpFile = __DIR__ . '/data/tmp/' . uniqid() . '.json';

        $this->crypt = $this->createMock(Crypt::class);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        if (file_exists($this->tmpFile)) {
            @unlink($this->tmpFile);
        }

        parent::tearDown();
    }
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Tests;

use Joomla\Crypt\Crypt;
use Joomla\Keychain\Keychain;
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
		if (file_exists($this->tmpFile))
		{
			@unlink($this->tmpFile);
		}

		parent::tearDown();
	}
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests\Password;

use Joomla\Authentication\Password\Argon2idHandler;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Authentication\Password\Argon2idHandler
 */
class Argon2idHandlerTest extends TestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 */
	public static function setUpBeforeClass(): void
	{
		if (!Argon2idHandler::isSupported())
		{
			static::markTestSkipped('Argon2id algorithm is not supported.');
		}

		parent::setUpBeforeClass();
	}

	/**
	 * @testdox  A password is hashed and validated
	 *
	 * @covers   Joomla\Authentication\Password\Argon2idHandler
	 */
	public function testAPasswordIsHashedAndValidated()
	{
		$handler = new Argon2idHandler;
		$hash = $handler->hashPassword('password');
		$this->assertTrue($handler->validatePassword('password', $hash), 'The hashed password was not validated.');
	}
}

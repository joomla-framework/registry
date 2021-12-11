<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests;

use Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy;
use Joomla\Authentication\Authentication;
use Joomla\Authentication\Password\HandlerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
 */
class AbstractUsernamePasswordAuthenticationStrategyTest extends TestCase
{
	/**
	 * @testdox  A user can be successfully authenticated
	 *
	 * @covers   Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
	 */
	public function testAuthenticateSuccess()
	{
		$handler = $this->createMock(HandlerInterface::class);
		$handler->expects($this->once())
			->method('validatePassword')
			->willReturn(true);

		$strategy = new class($handler) extends AbstractUsernamePasswordAuthenticationStrategy
		{
			public function authenticate()
			{
				return $this->doAuthenticate('username', 'password');
			}

			protected function getHashedPassword($username)
			{
				return 'password';
			}
		};

		$this->assertSame('username', $strategy->authenticate());
		$this->assertSame(Authentication::SUCCESS, $strategy->getResult(), 'The correct result status is set');
	}

	/**
	 * @testdox  A user cannot be authenticated when a password cannot be found for the username
	 *
	 * @covers   Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
	 */
	public function testAuthenticateNoUser()
	{
		$handler = $this->createMock(HandlerInterface::class);
		$handler->expects($this->never())
			->method('validatePassword');

		$strategy = new class($handler) extends AbstractUsernamePasswordAuthenticationStrategy
		{
			public function authenticate()
			{
				return $this->doAuthenticate('username', 'password');
			}

			protected function getHashedPassword($username)
			{
				return false;
			}
		};

		$this->assertFalse($strategy->authenticate());
		$this->assertSame(Authentication::NO_SUCH_USER, $strategy->getResult(), 'The correct result status is set');
	}

	/**
	 * @testdox  A user cannot be authenticated when the password cannot be validated
	 *
	 * @covers   Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
	 */
	public function testAuthenticateInvalidPassword()
	{
		$handler = $this->createMock(HandlerInterface::class);
		$handler->expects($this->once())
			->method('validatePassword')
			->willReturn(false);

		$strategy = new class($handler) extends AbstractUsernamePasswordAuthenticationStrategy
		{
			public function authenticate()
			{
				return $this->doAuthenticate('username', 'password');
			}

			protected function getHashedPassword($username)
			{
				return 'password';
			}
		};

		$this->assertFalse($strategy->authenticate());
		$this->assertSame(Authentication::INVALID_CREDENTIALS, $strategy->getResult(), 'The correct result status is set');
	}
}

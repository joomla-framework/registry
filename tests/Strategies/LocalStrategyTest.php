<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests\Strategies;

use Joomla\Authentication\Authentication;
use Joomla\Authentication\Password\HandlerInterface;
use Joomla\Authentication\Strategies\LocalStrategy;
use Joomla\Input\Input;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Authentication\Strategies\LocalStrategy
 */
class LocalStrategyTest extends TestCase
{
	/**
	 * @var  MockObject|Input
	 */
	private $input;

	/**
	 * @var  MockObject|HandlerInterface
	 */
	private $passwordHandler;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 */
	protected function setUp(): void
	{
		$this->input           = $this->createMock(Input::class);
		$this->passwordHandler = $this->createMock(HandlerInterface::class);

		parent::setUp();
	}

	/**
	 * Tests the authenticate method with valid credentials.
	 *
	 * @covers   Joomla\Authentication\Strategies\LocalStrategy
	 * @uses     Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
	 */
	public function testValidPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->any())
			->method('validatePassword')
			->willReturn(true);

		$credentialStore = [
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJG'
		];

		$localStrategy = new LocalStrategy($this->input, $credentialStore, $this->passwordHandler);

		$this->assertEquals('username', $localStrategy->authenticate());

		$this->assertEquals(Authentication::SUCCESS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with invalid credentials.
	 *
	 * @covers   Joomla\Authentication\Strategies\LocalStrategy
	 * @uses     Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
	 */
	public function testInvalidPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->any())
			->method('validatePassword')
			->willReturn(false);

		$credentialStore = [
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		];

		$localStrategy = new LocalStrategy($this->input, $credentialStore, $this->passwordHandler);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::INVALID_CREDENTIALS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with no credentials provided.
	 *
	 * @covers   Joomla\Authentication\Strategies\LocalStrategy
	 * @uses     Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
	 */
	public function testNoPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturn(false);

		$this->passwordHandler->expects($this->never())
			->method('validatePassword');

		$credentialStore = [
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		];

		$localStrategy = new LocalStrategy($this->input, $credentialStore, $this->passwordHandler);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::NO_CREDENTIALS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with credentials for an unknown user.
	 *
	 * @covers   Joomla\Authentication\Strategies\LocalStrategy
	 * @uses     Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
	 */
	public function testUserNotExist()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->never())
			->method('validatePassword');

		$credentialStore = [
			'jimbob' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		];

		$localStrategy = new LocalStrategy($this->input, $credentialStore, $this->passwordHandler);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::NO_SUCH_USER, $localStrategy->getResult());
	}
}

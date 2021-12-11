<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests\Strategies;

use Joomla\Authentication\Authentication;
use Joomla\Authentication\Password\HandlerInterface;
use Joomla\Authentication\Strategies\DatabaseStrategy;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\QueryInterface;
use Joomla\Input\Input;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Authentication\Strategies\DatabaseStrategy
 */
class DatabaseStrategyTest extends TestCase
{
	/**
	 * @var  MockObject|DatabaseDriver
	 */
	private $db;

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
		$this->db              = $this->createMock(DatabaseInterface::class);
		$this->input           = $this->createMock(Input::class);
		$this->passwordHandler = $this->createMock(HandlerInterface::class);

		parent::setUp();
	}

	/**
	 * Tests the authenticate method with valid credentials.
	 *
	 * @covers   Joomla\Authentication\Strategies\DatabaseStrategy
	 * @uses     Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
	 */
	public function testValidPassword()
	{
		$query = $this->createMock(QueryInterface::class);
		$query->expects($this->any())
			->method('select')
			->willReturnSelf();

		$query->expects($this->any())
			->method('from')
			->willReturnSelf();

		$query->expects($this->any())
			->method('where')
			->willReturnSelf();

		$query->expects($this->any())
			->method('bind')
			->willReturnSelf();

		$this->db->expects($this->any())
			->method('getQuery')
			->willReturn($query);

		$this->db->expects($this->once())
			->method('setQuery')
			->with($query)
			->willReturnSelf();

		$this->db->expects($this->once())
			->method('loadResult')
			->willReturn('$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJG');

		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->any())
			->method('validatePassword')
			->willReturn(true);

		$strategy = new DatabaseStrategy($this->input, $this->db, [], $this->passwordHandler);

		$this->assertEquals('username', $strategy->authenticate());
		$this->assertEquals(Authentication::SUCCESS, $strategy->getResult());
	}

	/**
	 * Tests the authenticate method with invalid credentials.
	 *
	 * @covers   Joomla\Authentication\Strategies\DatabaseStrategy
	 * @uses     Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
	 */
	public function testInvalidPassword()
	{
		$query = $this->createMock(QueryInterface::class);
		$query->expects($this->any())
			->method('select')
			->willReturnSelf();

		$query->expects($this->any())
			->method('from')
			->willReturnSelf();

		$query->expects($this->any())
			->method('where')
			->willReturnSelf();

		$query->expects($this->any())
			->method('bind')
			->willReturnSelf();

		$this->db->expects($this->any())
			->method('getQuery')
			->willReturn($query);

		$this->db->expects($this->once())
			->method('setQuery')
			->with($query)
			->willReturnSelf();

		$this->db->expects($this->once())
			->method('loadResult')
			->willReturn('$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH');

		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->any())
			->method('validatePassword')
			->willReturn(false);

		$strategy = new DatabaseStrategy($this->input, $this->db, [], $this->passwordHandler);

		$this->assertEquals(false, $strategy->authenticate());
		$this->assertEquals(Authentication::INVALID_CREDENTIALS, $strategy->getResult());
	}

	/**
	 * Tests the authenticate method with no credentials provided.
	 *
	 * @covers   Joomla\Authentication\Strategies\DatabaseStrategy
	 * @uses     Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
	 */
	public function testNoPassword()
	{
		$this->db->expects($this->never())
			->method('setQuery');

		$this->input->expects($this->any())
			->method('get')
			->willReturn(false);

		$this->passwordHandler->expects($this->never())
			->method('validatePassword');

		$strategy = new DatabaseStrategy($this->input, $this->db, [], $this->passwordHandler);

		$this->assertEquals(false, $strategy->authenticate());
		$this->assertEquals(Authentication::NO_CREDENTIALS, $strategy->getResult());
	}

	/**
	 * Tests the authenticate method with credentials for an unknown user.
	 *
	 * @covers   Joomla\Authentication\Strategies\DatabaseStrategy
	 * @uses     Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy
	 */
	public function testUserNotExist()
	{
		$query = $this->createMock(QueryInterface::class);
		$query->expects($this->any())
			->method('select')
			->willReturnSelf();

		$query->expects($this->any())
			->method('from')
			->willReturnSelf();

		$query->expects($this->any())
			->method('where')
			->willReturnSelf();

		$query->expects($this->any())
			->method('bind')
			->willReturnSelf();

		$this->db->expects($this->any())
			->method('getQuery')
			->willReturn($query);

		$this->db->expects($this->once())
			->method('setQuery')
			->with($query)
			->willReturnSelf();

		$this->db->expects($this->once())
			->method('loadResult')
			->willReturn(null);

		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->never())
			->method('validatePassword');

		$strategy = new DatabaseStrategy($this->input, $this->db, [], $this->passwordHandler);

		$this->assertEquals(false, $strategy->authenticate());
		$this->assertEquals(Authentication::NO_SUCH_USER, $strategy->getResult());
	}
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Crypt\Tests;

use Joomla\Crypt\PasswordInterface;
use Joomla\Crypt\Password\PasswordSimple;
use Joomla\Test\TestHelper;

/**
 * Test class for \Joomla\Crypt\Password\PasswordSimpleSimple.
 *
 * @since  1.0
 */
class PasswordSimpleTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Data provider for testCreate method.
	 *
	 * @return  array
	 *
	 * @since   1.3.0
	 */
	public function createData()
	{
		// Password, type, salt, expected cost
		return array(
			'Blowfish' => array('password', PasswordInterface::BLOWFISH, 'ABCDEFGHIJKLMNOPQRSTUV',
				'$2y$10$ABCDEFGHIJKLMNOPQRSTUOiAi7OcdE4zRCh6NcGWusEcNPtq6/w8.'),

			'Blowfish2' => array('password', '$2a$', 'ABCDEFGHIJKLMNOPQRSTUV',
				'$2y$10$ABCDEFGHIJKLMNOPQRSTUOiAi7OcdE4zRCh6NcGWusEcNPtq6/w8.'),

			'MD5' => array('password', PasswordInterface::MD5, 'ABCDEFGHIJKL',
				'$1$ABCDEFGH$hGGndps75hhROKqu/zh9q1'),

			'Blowfish_5' => array('password', PasswordInterface::BLOWFISH, 'ABCDEFGHIJKLMNOPQRSTUV',
				'$2y$05$ABCDEFGHIJKLMNOPQRSTUOvv7EU5o68GAoLxyfugvULZR70IIMZqW', 5),

			'default' => array('password', null, 'ABCDEFGHIJKLMNOPQRSTUV',
				'$2y$05$ABCDEFGHIJKLMNOPQRSTUOvv7EU5o68GAoLxyfugvULZR70IIMZqW', 5)
		);
	}

	/**
	 * Data provider for testCreateException method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function createExceptionData()
	{
		return array(
			'Bogus' => array('password', 'abc', 'ABCDEFGHIJKLMNOPQRSTUV', '$2y$10$ABCDEFGHIJKLMNOPQRSTUOiAi7OcdE4zRCh6NcGWusEcNPtq6/w8.', 10),
		);
	}

	/**
	 * Tests create method for expected exception
	 *
	 * @param   string   $password  The password to create
	 * @param   string   $type      The type of hash
	 * @param   string   $salt      The salt to be used
	 * @param   string   $expected  The expected result
	 * @param   integer  $cost      The cost value
	 *
	 * @return void
	 *
	 * @covers  Joomla\Crypt\Password\PasswordSimple::create
	 * @dataProvider  createExceptionData
	 * @expectedException  \InvalidArgumentException
	 * @since  1.0
	 */
	public function testCreateException($password, $type, $salt, $expected, $cost)
	{
		$hasher = $this->getMock('Joomla\\Crypt\\Password\\PasswordSimple',
			array('getSalt')
		);
		$hasher->setCost($cost);

		$hasher->expects($this->any())
			->method('getSalt')
			->with(strlen($salt))
			->will($this->returnValue($salt));

		$this->assertEquals(
			$expected,
			$hasher->create($password, $type)
		);
	}

	/**
	 * Tests the Joomla\Crypt\Password\PasswordSimple::Create method.
	 *
	 * @param   string   $password  The password to create
	 * @param   string   $type      The type of hash
	 * @param   string   $salt      The salt to be used
	 * @param   string   $expected  The expected result
	 * @param   integer  $cost      The cost value
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Password\PasswordSimple::create
	 * @dataProvider  createData
	 * @since   1.0
	 */
	public function testCreate($password, $type, $salt, $expected, $cost = 10)
	{
		$hasher = $this->getMock('Joomla\\Crypt\\Password\\PasswordSimple',
			array('getSalt')
		);

		$hasher->setCost($cost);

		$hasher->expects($this->any())
			->method('getSalt')
			->with(strlen($salt))
			->will($this->returnValue($salt));

		$this->assertEquals(
			$expected,
			$hasher->create($password, $type)
		);
	}

	/**
	 * Tests the Joomla\Crypt\Password\PasswordSimple::setCost method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Password\PasswordSimple::setCost
	 * @since   1.3.0
	 */
	public function testSetCost()
	{
		$hasher = new PasswordSimple;

		$this->assertNull(
			$hasher->setCost(10)
		);

		$this->assertEquals(
			10,
			TestHelper::getValue($hasher, 'cost')
		);
	}

	/**
	 * Data Provider for testVerify.
	 *
	 * @return array
	 *
	 * @since  1.3.0
	 */
	public function verifyData()
	{
		// Password, hash, expected
		return array(
			'Blowfish Valid:' => array('password', '$2y$10$ABCDEFGHIJKLMNOPQRSTUOiAi7OcdE4zRCh6NcGWusEcNPtq6/w8.', true),
			'Blowfish Invalid:' => array('wrong password', '$2y$10$ABCDEFGHIJKLMNOPQRSTUOiAi7OcdE4zRCh6NcGWusEcNPtq6/w8.', false),
			'MD5 Valid' => array('password', '$1$ABCDEFGH$hGGndps75hhROKqu/zh9q1', true),
			'MD5 Invalid' => array('passw0rd', '$1$ABCDEFGH$hGGndps75hhROKqu/zh9q1', false),
			'Invalid' => array('passw0rd', 'foo bar', false)
		);
	}

	/**
	 * Tests the verify method.
	 *
	 * @param   string  $password     The password to verify
	 * @param   string  $hash         The hash
	 * @param   string  $expectation  The expected result
	 *
	 * @return  void
	 *
	 * @covers        Joomla\Crypt\Password\PasswordSimple::verify
	 * @dataProvider  verifyData
	 * @since   1.3.0
	 */
	public function testVerify($password, $hash, $expectation)
	{
		$hasher = new PasswordSimple;

		$this->assertEquals(
			$expectation,
			$hasher->verify($password, $hash)
		);
	}

	/**
	 * Data Provider for testDefaultType
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function defaultTypeData()
	{
		// Type, expectation
		return array(
			'Null' => array('','$2y$'),
		);
	}

	/**
	 * Tests the setDefaultType method.
	 *
	 * @param   string  $type         The proposed default type
	 * @param   string  $expectation  The expected value of $this->defaultType
	 *
	 * @return  void
	 *
	 * @covers        Joomla\Crypt\Password\PasswordSimple::setDefaultType
	 * @dataProvider  defaultTypeData
	 * @since   1.0
	 */
	public function testSetDefaultType($type, $expectation)
	{
		$test = new PasswordSimple;
		$test->setDefaultType($type);
		$this->assertEquals(
			$expectation,
			TestHelper::getValue($test, 'defaultType')
		);
	}

	/**
	 * Tests the getDefaultType method.
	 *
	 * @param   string  $type         The proposed default type
	 * @param   string  $expectation  The expected value of $this->defaultType
	 *
	 * @return  void
	 *
	 * @covers        Joomla\Crypt\Password\PasswordSimple::getDefaultType
	 * @dataProvider  defaultTypeData
	 * @since   1.0
	 */
	public function testGetDefaultType($type, $expectation)
	{
		$test = new PasswordSimple;
		$test->setDefaultType($type);

		$this->assertEquals(
			$expectation,
			$test->getDefaultType()
		);
	}

	/**
	 * Tests the getSalt method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Password\PasswordSimple::getSalt
	 * @since   1.3.0
	 */
	public function testGetSalt()
	{
		// We're just testing wether the value has the expected length.
		// We obviously can't test the result since it's random.

		$password = new PasswordSimple;

		$salt16 = TestHelper::invoke($password, 'getSalt', 16);
		$this->assertEquals(
			16,
			strlen($salt16)
		);

		$salt8 = TestHelper::invoke($password, 'getSalt', 8);
		$this->assertEquals(
			8,
			strlen($salt8)
		);

		$salt17 = TestHelper::invoke($password, 'getSalt', 17);
		$this->assertEquals(
			17,
			strlen($salt17)
		);
	}
}

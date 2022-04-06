<?php
/**
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Input\Tests;

use Joomla\Filter\InputFilter;
use Joomla\Input\Cookie;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

abstract class CookieDataStore
{
	private static $store = [];

	public static function reset(): void
	{
		self::$store = [];
	}

	public static function has(string $key): bool
	{
		return isset(self::$store[$key]);
	}

	public static function set(string $key, $value): void
	{
		self::$store[$key] = $value;
	}
}

/**
 * Test class for \Joomla\Input\Cookie.
 */
class CookieTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		CookieDataStore::reset();
	}

	/**
	 * @testdox  Tests the input creates itself properly
	 *
	 * @covers   Joomla\Input\Cookie
	 * @uses     Joomla\Input\Input
	 */
	public function test__constructDefaultBehaviour()
	{
		$instance = new Cookie;

		$this->assertSame($_COOKIE, TestHelper::getValue($instance, 'data'), 'The Cookie input defaults to the $_COOKIE superglobal');
		$this->assertInstanceOf(InputFilter::class, TestHelper::getValue($instance, 'filter'), 'The Input object should create an InputFilter if one is not provided');
	}

	/**
	 * @testdox  Tests the constructor with injected data
	 *
	 * @covers   Joomla\Input\Cookie
	 * @uses     Joomla\Input\Input
	 */
	public function test__constructDependencyInjection()
	{
		$src        = ['foo' => 'bar'];
		$mockFilter = $this->createMock(InputFilter::class);

		$instance = new Cookie($src, ['filter' => $mockFilter]);

		$this->assertSame($src, TestHelper::getValue($instance, 'data'));
		$this->assertSame($mockFilter, TestHelper::getValue($instance, 'filter'));
	}

	/**
	 * @testdox  Tests that data is correctly set with the legacy signature
	 *
	 * @covers   Joomla\Input\Cookie
	 * @uses     Joomla\Input\Input
	 */
	public function testSetWithLegacySignature()
	{
		$mockFilter = $this->createMock(InputFilter::class);

		$instance = new Cookie([], ['filter' => $mockFilter]);
		$instance->set('foo', 'bar', 15);

		$this->assertTrue(CookieDataStore::has('foo'));
	}

	/**
	 * @testdox  Tests that data is correctly set with the new signature
	 *
	 * @covers   Joomla\Input\Cookie
	 * @uses     Joomla\Input\Input
	 */
	public function testSetWithNewSignature()
	{
		$mockFilter = $this->createMock(InputFilter::class);

		$instance = new Cookie([], ['filter' => $mockFilter]);
		$instance->set('foo', 'bar', ['expire' => 15, 'samesite' => 'Strict']);

		$this->assertTrue(CookieDataStore::has('foo'));
	}
}

// Stub for setcookie
namespace Joomla\Input;

use Joomla\Input\Tests\CookieDataStore;

if (version_compare(PHP_VERSION, '7.3', '>='))
{
	/**
	 * Stub.
	 *
	 * @param   string  $name     Name
	 * @param   string  $value    Value
	 * @param   array   $options  Expire
	 *
	 * @return  bool
	 *
	 * @since   1.1.4
	 */
	function setcookie($name, $value, $options = [])
	{
		CookieDataStore::set($name, $value);

		return true;
	}
}
else
{
	/**
	 * Stub.
	 *
	 * @param   string  $name      Name
	 * @param   string  $value     Value
	 * @param   int     $expire    Expire
	 * @param   string  $path      Path
	 * @param   string  $domain    Domain
	 * @param   bool    $secure    Secure
	 * @param   bool    $httpOnly  HttpOnly
	 *
	 * @return  bool
	 *
	 * @since   1.1.4
	 */
	function setcookie($name, $value, $expire = 0, $path = '', $domain = '', $secure = false, $httpOnly = false)
	{
		CookieDataStore::set($name, $value);

		return true;
	}
}

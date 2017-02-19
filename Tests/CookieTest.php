<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Input\Tests;

use Joomla\Input\Cookie;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Input\Cookie.
 */
class CookieTest extends TestCase
{
	/**
	 * @testdox  Tests the default constructor behavior
	 *
	 * @covers   Joomla\Input\Cookie::__construct
	 */
	public function test__constructDefaultBehaviour()
	{
		$instance = new Cookie;

		$this->assertAttributeSame($_COOKIE, 'data', $instance);
		$this->assertAttributeInstanceOf('Joomla\Filter\InputFilter', 'filter', $instance);
	}

	/**
	 * @testdox  Tests the constructor with injected data
	 *
	 * @covers   Joomla\Input\Cookie::__construct
	 */
	public function test__constructDependencyInjection()
	{
		$src        = ['foo' => 'bar'];
		$mockFilter = $this->getMock('Joomla\Filter\InputFilter');

		$instance = new Cookie($src, ['filter' => $mockFilter]);

		$this->assertAttributeSame($src, 'data', $instance);
		$this->assertAttributeSame($mockFilter, 'filter', $instance);
	}

	/**
	 * @testdox  Tests that data is correctly set
	 *
	 * @covers   Joomla\Input\Cookie::set
	 * @uses     Joomla\Input\Cookie::__construct
	 */
	public function testSet()
	{
		$mockFilter = $this->getMock('Joomla\Filter\InputFilter');

		$instance = new Cookie(null, ['filter' => $mockFilter]);
		$instance->set('foo', 'bar');

		$this->assertAttributeContains('bar', 'data', $instance);
	}
}

// Stub for setcookie
namespace Joomla\Input;

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
 * @return  void
 *
 * @since   1.1.4
 */
function setcookie($name, $value, $expire = 0, $path = '', $domain = '', $secure = false, $httpOnly = false)
{
	return true;
}

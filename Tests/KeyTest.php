<?php
/**
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Crypt\Tests;

use Joomla\Crypt\Key;

/**
 * Test class for \Joomla\Crypt\Key.
 *
 * @since  1.0
 */
class KeyTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test __construct()
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Key::__construct()
	 * @since   __DEPLOY_VERSION__
	 */
	public function test__construct()
	{
		$key = new Key('simple');

		$this->assertEquals(
			'simple',
			$key->type,
			'Line:' . __LINE__ . ' type of key should be assgined correctly.'
		);

		$this->assertNull(
			$key->private,
			'Line:' . __LINE__ . ' private key should be null if none given.'
		);

		$this->assertNull(
			$key->public,
			'Line:' . __LINE__ . ' public key should be null if none given.'
		);

		$key = new Key('simple', 'foo', 'bar');

		$this->assertEquals(
			'simple',
			$key->type,
			'Line:' . __LINE__ . ' type of key should be assgined correctly.'
		);

		$this->assertEquals(
			'foo',
			$key->private,
			'Line:' . __LINE__ . ' public key should be assgined correctly.'
		);

		$this->assertEquals(
			'bar',
			$key->public,
			'Line:' . __LINE__ . ' public key should be assgined correctly.'
		);
	}

	/**
	 * Test __get()
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Key::__get()
	 * @expectedException  PHPUnit_Framework_Error
	 * @since   __DEPLOY_VERSION__
	 */
	public function test__get()
	{
		$key = new Key('simple');

		$this->assertEquals(
			'simple',
			$key->type
		);

		// Throws PHP error which is tested by expected exception.
		$this->assertEquals(
			'',
			$key->foobar
		);
	}
}

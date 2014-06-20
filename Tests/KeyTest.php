<?php
/**
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Crypt\Tests;

use Joomla\Crypt\Key;

/**
 * Test class for JCrypt.
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
	 * @since  __VERSION_NO__
	 */
	public function test__construct()
	{
		$key = new Key('simple');

		$this->assertEquals(
			$key->type,
			'simple',
			'Line:' . __LINE__ . ' type of key should be assgined correctly.'
		);

		$this->assertEquals(
			$key->private,
			null,
			'Line:' . __LINE__ . ' private key should be null if none given.'
		);

		$this->assertEquals(
			$key->public,
			null,
			'Line:' . __LINE__ . ' public key should be null if none given.'
		);

		$key = new Key('simple', 'foo', 'bar');

		$this->assertEquals(
			$key->type,
			'simple',
			'Line:' . __LINE__ . ' type of key should be assgined correctly.'
		);

		$this->assertEquals(
			$key->private,
			'foo',
			'Line:' . __LINE__ . ' public key should be assgined correctly.'
		);

		$this->assertEquals(
			$key->public,
			'bar',
			'Line:' . __LINE__ . ' public key should be assgined correctly.'
		);
	}

	/**
	 * Test __get()
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Key::__get()
	 * @expectedException PHPUnit_Framework_Error
	 * @since  __VERSION_NO__
	 */
	public function test__get()
	{
		$key = new Key('simple');

		$this->assertEquals(
			$key->type,
			'simple'
		);

		// Throws PHP error which is tested by expected exception.
		$this->assertEquals(
			$key->foobar,
			''
		);
	}
}

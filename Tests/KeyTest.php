<?php
/**
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Crypt\Tests;

use Joomla\Crypt\Key;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Crypt\Key.
 */
class KeyTest extends TestCase
{
	/**
	 * Object under testing
	 *
	 * @var  Key
	 */
	private $object;

	/**
	 * Private key for the test object
	 *
	 * @var string
	 */
	private $private = 'private';

	/**
	 * Public key for the test object
	 *
	 * @var string
	 */
	private $public = 'public';

	/**
	 * Key type for the test object
	 *
	 * @var string
	 */
	private $type = 'test';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->type    = 'test';
		$this->public  = 'public';
		$this->private = 'private';

		$this->object = new Key($this->type, $this->private, $this->public);
	}

	/**
	 * @testdox  Validates the object is instantiated correctly
	 *
	 * @covers   \Joomla\Crypt\Key::__construct()
	 */
	public function test__construct()
	{
		$this->assertAttributeSame($this->type, 'type', $this->object);
		$this->assertAttributeSame($this->public, 'public', $this->object);
		$this->assertAttributeSame($this->private, 'private', $this->object);
	}

	/**
	 * @testdox  Validates the private key is returned
	 *
	 * @covers   \Joomla\Crypt\Key::getPrivateKey()
	 */
	public function testGetPrivateKey()
	{
		$this->assertSame($this->private, $this->object->getPrivate());
	}

	/**
	 * @testdox  Validates the public key is returned
	 *
	 * @covers   \Joomla\Crypt\Key::getPublicKey()
	 */
	public function testGetPublicKey()
	{
		$this->assertSame($this->public, $this->object->getPublic());
	}

	/**
	 * @testdox  Validates the key type is returned
	 *
	 * @covers   \Joomla\Crypt\Key::getType()
	 */
	public function testGetKeyType()
	{
		$this->assertSame($this->type, $this->object->getType());
	}
}

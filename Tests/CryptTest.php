<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Crypt\Tests;

use Joomla\Crypt\Crypt;
use Joomla\Crypt\Key;

/**
 * Test class for \Joomla\Crypt\Crypt.
 *
 * @since  1.0
 */
class CryptTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Crypt
	 * @since  1.0
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Crypt;

		$key = new Key('simple');
		$key->private = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCUgkVF4mLxAUf80ZJPAJHXHoac';
		$key->public = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCUgkVF4mLxAUf80ZJPAJHXHoac';

		$this->object->setKey($key);
	}

	/**
	 * Test __construct()
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Crypt::__construct()
	 * @since   __DEPLOY_VERSION__
	 */
	public function test__construct()
	{
		$this->assertAttributeInstanceOf(
			'Joomla\\Crypt\\CipherInterface',
			'cipher',
			$this->object
		);

		$this->assertAttributeInstanceOf(
			'Joomla\\Crypt\\Key',
			'key',
			$this->object
		);
	}

	/**
	 * Test...
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function dataForEncrypt()
	{
		return array(
			array(
				'1.txt',
				'c-;3-(Is>{DJzOHMCv_<#yKuN/G`/Us{GkgicWG$M|HW;kI0BVZ^|FY/"Obt53?PNaWwhmRtH;lWkWE4vlG5CIFA!abu&F=Xo#Qw}gAp3;GL\'k])%D}C+W&ne6_F$3P5'),
			array(
				'2.txt',
				'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ' .
					'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor ' .
					'in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt ' .
					'in culpa qui officia deserunt mollit anim id est laborum.'),
			array('3.txt', 'لا أحد يحب الألم بذاته، يسعى ورائه أو يبتغيه، ببساطة لأنه الألم...'),
			array('4.txt',
				'Широкая электрификация южных губерний даст мощный ' .
					'толчок подъёму сельского хозяйства'),
			array('5.txt', 'The quick brown fox jumps over the lazy dog.')
		);
	}

	/**
	 * Test...
	 *
	 * @param   string  $file  @todo
	 * @param   string  $data  @todo
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Crypt::decrypt
	 * @dataProvider dataForEncrypt
	 * @since   __DEPLOY_VERSION__
	 */
	public function testDecrypt($file, $data)
	{
		$encrypted = file_get_contents(__DIR__ . '/Cipher/stubs/encrypted/simple/' . $file);
		$decrypted = $this->object->decrypt($encrypted);

		// Assert that the decrypted values are the same as the expected ones.
		$this->assertEquals(
			$data,
			$decrypted
		);
	}

	/**
	 * Test...
	 *
	 * @param   string  $file  @todo
	 * @param   string  $data  @todo
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Crypt::encrypt
	 * @dataProvider  dataForEncrypt
	 * @since   __DEPLOY_VERSION__
	 */
	public function testEncrypt($file, $data)
	{
		$encrypted = $this->object->encrypt($data);

		// Assert that the encrypted value is not the same as the clear text value.
		$this->assertNotEquals(
			$data,
			$encrypted
		);

		// Assert that the encrypted values are the same as the expected ones.
		$this->assertStringEqualsFile(
			__DIR__ . '/Cipher/stubs/encrypted/simple/' . $file,
			$encrypted
		);
	}

	/**
	 * Test...
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Crypt::generateKey
	 * @since   __DEPLOY_VERSION__
	 */
	public function testGenerateKey()
	{
		$key = $this->object->generateKey();

		$this->assertInstanceOf(
			'Joomla\\Crypt\\Key',
			$key
		);
	}

	/**
	 * Test...
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Crypt::setKey
	 * @since   __DEPLOY_VERSION__
	 */
	public function testSetKey()
	{
		$keyMock = $this->getMock('Joomla\\Crypt\\Key', array(), array('simple'));

		$this->object->setKey($keyMock);

		$this->assertAttributeEquals(
			$keyMock,
			'key',
			$this->object
		);
	}

	/**
	 * Test...
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Crypt::genRandomBytes
	 * @since   __DEPLOY_VERSION__
	 */
	public function testGenRandomBytes()
	{
		// We're just testing wether the value has the expected length.
		// We obviously can't test the result since it's random.

		$randomBytes16 = Crypt::genRandomBytes();
		$this->assertEquals(
			16,
			strlen($randomBytes16)
		);

		$randomBytes8 = Crypt::genRandomBytes(8);
		$this->assertEquals(
			8,
			strlen($randomBytes8)
		);

		$randomBytes17 = Crypt::genRandomBytes(17);
		$this->assertEquals(
			17,
			strlen($randomBytes17)
		);
	}

	/**
	 * Test...
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Crypt\Crypt::genRandomBytesCustom
	 * @since   __DEPLOY_VERSION__
	 */
	public function testGenRandomBytesCustom()
	{
		// We're just testing wether the value has the expected length.
		// We obviously can't test the result since it's random.

		$randomBytes16 = Crypt::genRandomBytesCustom();
		$this->assertEquals(
			16,
			strlen($randomBytes16)
		);

		$randomBytes8 = Crypt::genRandomBytesCustom(8);
		$this->assertEquals(
			8,
			strlen($randomBytes8)
		);

		$randomBytes17 = Crypt::genRandomBytesCustom(17);
		$this->assertEquals(
			17,
			strlen($randomBytes17)
		);
	}
}

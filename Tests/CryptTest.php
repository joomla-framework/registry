<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Crypt\Tests;

use Joomla\Crypt\Cipher\Crypto;
use Joomla\Crypt\Crypt;
use Joomla\Crypt\Key;
use Symfony\Polyfill\Util\Binary;

/**
 * Test class for \Joomla\Crypt\Crypt.
 *
 * @since  1.0
 */
class CryptTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Cipher used for testing
	 *
	 * @var  Crypto
	 */
	private $cipher;

	/**
	 * Generated key for testing
	 *
	 * @var  Key
	 */
	private $key;

	/**
	 * Object under testing
	 *
	 * @var  Crypt
	 */
	private $object;

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		// Only run the test if the environment supports it.
		try
		{
			\Crypto::RuntimeTest();
		}
		catch (\CryptoTestFailedException $e)
		{
			self::markTestSkipped('The environment cannot safely perform encryption with this cipher.');
		}
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->cipher = new Crypto;
		$this->key    = $this->cipher->generateKey();

		$this->object = new Crypt($this->cipher, $this->key);
	}

	/**
	 * @testdox  Validates the object is instantiated correctly
	 *
	 * @covers   \Joomla\Crypt\Crypt::__construct()
	 */
	public function test__construct()
	{
		$this->assertAttributeSame(
			$this->cipher,
			'cipher',
			$this->object
		);

		$this->assertAttributeSame(
			$this->key,
			'key',
			$this->object
		);
	}

	/**
	 * Test data for processing
	 *
	 * @return  array
	 */
	public function dataStrings()
	{
		return array(
			array('c-;3-(Is>{DJzOHMCv_<#yKuN/G`/Us{GkgicWG$M|HW;kI0BVZ^|FY/"Obt53?PNaWwhmRtH;lWkWE4vlG5CIFA!abu&F=Xo#Qw}gAp3;GL\'k])%D}C+W&ne6_F$3P5'),
			array('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ' .
					'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor ' .
					'in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt ' .
					'in culpa qui officia deserunt mollit anim id est laborum.'),
			array('لا أحد يحب الألم بذاته، يسعى ورائه أو يبتغيه، ببساطة لأنه الألم...'),
			array('Широкая электрификация южных губерний даст мощный толчок подъёму сельского хозяйства'),
			array('The quick brown fox jumps over the lazy dog.')
		);
	}

	/**
	 * @testdox  Validates data is encrypted and decrypted correctly
	 *
	 * @param   string  $data  The decrypted data to validate
	 *
	 * @covers        \Joomla\Crypt\Crypt::decrypt
	 * @covers        \Joomla\Crypt\Crypt::encrypt
	 * @dataProvider  dataStrings
	 */
	public function testDataEncryptionAndDecryption($data)
	{
		$cipher = new Crypto;
		$key    = $cipher->generateKey();

		$encrypted = $cipher->encrypt($data, $key);

		// Assert that the encrypted value is not the same as the clear text value.
		$this->assertNotSame($data, $encrypted);

		$decrypted = $cipher->decrypt($encrypted, $key);

		// Assert the decrypted string is the same value we started with
		$this->assertSame($data, $decrypted);
	}

	/**
	 * @testdox  Validates keys are correctly generated
	 *
	 * @covers   \Joomla\Crypt\Crypt::generateKey
	 */
	public function testGenerateKey()
	{
		$key = $this->object->generateKey();

		// Assert that the key is the correct type.
		$this->assertInstanceOf('Joomla\Crypt\Key', $key);

		// Assert the private key is our expected value.
		$this->assertSame('unused', $key->getPrivate());

		// Assert the public key is the expected length
		$this->assertSame(\Crypto::KEY_BYTE_SIZE, Binary::strlen($key->getPublic()));

		// Assert the key is of the correct type.
		$this->assertSame('crypto', $key->getType());
	}

	/**
	 * @testdox  Validates keys are correctly set
	 *
	 * @covers   \Joomla\Crypt\Crypt::setKey
	 */
	public function testSetKey()
	{
		$keyMock = $this->getMockBuilder('Joomla\\Crypt\\Key')
			->setConstructorArgs(array('test', 'private', 'public'))
			->getMock();

		$this->object->setKey($keyMock);

		$this->assertAttributeNotSame(
			$this->key,
			'key',
			$this->object,
			'The new key did not replace the existing key.'
		);
	}

	/**
	 * Test data for processing
	 *
	 * @return  array
	 */
	public function dataRandomByteLength()
	{
		return array(
			'8 bytes' => array(8),
			'16 bytes' => array(16),
			'24 bytes' => array(24),
			'32 bytes' => array(32),
			'40 bytes' => array(40),
		);
	}

	/**
	 * @testdox  Validates a string of random bytes of the requested size is returned
	 *
	 * @param    integer  $length  The length of the random string to generate
	 *
	 * @covers        Joomla\Crypt\Crypt::genRandomBytes
	 * @dataProvider  dataRandomByteLength
	 */
	public function testGenRandomBytes($length)
	{
		$this->assertSame(
			$length,
			strlen(Crypt::genRandomBytes($length))
		);
	}
}

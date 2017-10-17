<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Crypt\Tests\Cipher;

use Joomla\Crypt\Cipher\OpenSSL;
use Joomla\Crypt\Key;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Crypt\Cipher\OpenSSL.
 */
class OpenSSLTest extends TestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		// Only run the test if the environment supports it.
		if (!OpenSSL::isSupported())
		{
			self::markTestSkipped('The environment cannot safely perform encryption with this cipher.');
		}
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
	 * @covers        Joomla\Crypt\Cipher\OpenSSL::decrypt
	 * @covers        Joomla\Crypt\Cipher\OpenSSL::encrypt
	 * @dataProvider  dataStrings
	 */
	public function testDataEncryptionAndDecryption($data)
	{
		$cipher = new OpenSSL('1234567890123456', 'aes-128-cbc');
		$key    = $cipher->generateKey(['passphrase' => __DIR__ . '/stubs/openssl-passphrase.dat']);

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
	 * @covers   Joomla\Crypt\Cipher\OpenSSL::generateKey
	 */
	public function testGenerateKey()
	{
		$passphraseFile = __DIR__ . '/stubs/openssl-passphrase.dat';

		$cipher = new OpenSSL('1234567890123456', 'aes-128-cbc');
		$key    = $cipher->generateKey(['passphrase' => $passphraseFile]);

		// Assert that the key is the correct type.
		$this->assertInstanceOf(Key::class, $key);

		// Assert the keys pass validation
		$this->assertSame($key->getPrivate(), $passphraseFile);
		$this->assertSame($key->getPublic(), 'unused');

		// Assert the key is of the correct type.
		$this->assertSame('openssl', $key->getType());
	}
}

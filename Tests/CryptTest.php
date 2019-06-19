<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Crypt\Tests;

use Joomla\Crypt\CipherInterface;
use Joomla\Crypt\Crypt;
use Joomla\Crypt\Key;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Crypt\Crypt.
 */
class CryptTest extends TestCase
{
	/**
	 * Cipher used for testing
	 *
	 * @var  MockObject|CipherInterface
	 */
	private $cipher;

	/**
	 * Generated key for testing
	 *
	 * @var  MockObject|Key
	 */
	private $key;

	/**
	 * Object under testing
	 *
	 * @var  Crypt
	 */
	private $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->cipher = $this->createMock(CipherInterface::class);
		$this->key    = $this->createMock(Key::class);

		$this->object = new Crypt($this->cipher, $this->key);
	}

	/**
	 * @testdox  Validates data is encrypted and decrypted correctly
	 */
	public function testDataEncryptionAndDecryption()
	{
		$decrypted = 'decrypt';
		$encrypted = 'encrypt';

		$this->cipher->expects($this->once())
			->method('encrypt')
			->with($decrypted)
			->willReturn($encrypted);

		$this->cipher->expects($this->once())
			->method('decrypt')
			->with($encrypted)
			->willReturn($decrypted);

		$this->object->encrypt($decrypted);
		$this->object->decrypt($encrypted);
	}

	/**
	 * @testdox  Validates keys are correctly generated
	 */
	public function testGenerateKey()
	{
		$this->cipher->expects($this->once())
			->method('generateKey')
			->willReturn($this->createMock(Key::class));

		$this->object->generateKey();
	}

	/**
	 * @testdox  Validates a new key can be set
	 */
	public function testSetKey()
	{
		$key = $this->createMock(Key::class);

		$this->object->setKey($key);

		$property = (new \ReflectionClass($this->object))->getProperty('key');
		$property->setAccessible(true);

		$this->assertSame($key, $property->getValue($this->object));
	}

	/**
	 * Test data for processing
	 *
	 * @return  \Generator
	 */
	public function dataRandomByteLength(): \Generator
	{
		yield '8 bytes' => [8];
		yield '16 bytes' => [16];
		yield '24 bytes' => [24];
		yield '32 bytes' => [32];
		yield '40 bytes' => [40];
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
			\strlen(Crypt::genRandomBytes($length))
		);
	}
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Tests;

use Joomla\Crypt\Crypt;
use Joomla\Keychain\Keychain;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Keychain\Keychain.
 */
class KeychainTest extends TestCase
{
	/**
	 * The mock Crypt object
	 *
	 * @var  Crypt|\PHPUnit_Framework_MockObject_MockObject
	 */
	private $crypt;

	/**
	 * The Keychain for testing
	 *
	 * @var  Keychain
	 */
	private $keychain;

	/**
	 * The temporary file used for validating a successful save
	 *
	 * @var  string
	 */
	private $tmpFile;

	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->tmpFile = __DIR__ . '/data/tmp/' . uniqid() . '.json';

		$this->crypt    = $this->getMockBuilder(Crypt::class)->getMock();
		$this->keychain = new Keychain($this->crypt);
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		if (file_exists($this->tmpFile))
		{
			@unlink($this->tmpFile);
		}

		parent::tearDown();
	}

	/**
	 * @covers  \Joomla\Keychain\Keychain::deleteValue
	 */
	public function testAValueIsDeletedFromTheKeychain()
	{
		$this->keychain->set('foo', 'bar');

		$this->assertSame('bar', $this->keychain->deleteValue('foo'), 'When a key is deleted the value it had is returned');
	}

	/**
	 * @covers  \Joomla\Keychain\Keychain::deleteValue
	 */
	public function testAKeychainIsLoadedFromAFile()
	{
		$this->crypt->expects($this->once())
			->method('decrypt')
			->willReturnArgument(0);

		$this->assertSame(
			$this->keychain,
			$this->keychain->loadKeychain(__DIR__ . '/data/keychain.json'),
			'When a file is loaded into the keychain the current instance is returned'
		);
	}

	/**
	 * @covers  \Joomla\Keychain\Keychain::deleteValue
	 * @expectedException  \RuntimeException
	 * @expectedExceptionMessage  Attempting to load non-existent keychain file
	 */
	public function testAKeychainCannotBeLoadedFromANonExistingFile()
	{
		$this->crypt->expects($this->never())
			->method('decrypt');

		$this->keychain->loadKeychain(__DIR__ . '/data/does-not-exist.json');
	}

	/**
	 * @covers  \Joomla\Keychain\Keychain::deleteValue
	 */
	public function testAKeychainIsSavedToAFile()
	{
		$this->crypt->expects($this->once())
			->method('encrypt')
			->willReturnArgument(0);

		$this->assertNotFalse(
			$this->keychain->saveKeychain($this->tmpFile),
			'A keychain should be saved to the filesystem successfully'
		);
	}

	/**
	 * @covers  \Joomla\Keychain\Keychain::deleteValue
	 * @expectedException  \RuntimeException
	 * @expectedExceptionMessage  A keychain file must be specified
	 */
	public function testAKeychainCannotBeSavedToTheFilesystemIfAnEmptyPathIsGiven()
	{
		$this->crypt->expects($this->never())
			->method('encrypt');

		$this->keychain->saveKeychain('');
	}
}

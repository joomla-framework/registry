<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\Folder;
use PHPUnit\Framework\TestCase;

/**
 * Base test case for filesystem interacting tests
 */
class FilesystemTestCase extends TestCase
{
	/**
	 * Path to the test space
	 *
	 * @var  null|string
	 */
	protected $testPath = null;

	/**
	 * Storage for the system's umask
	 *
	 * @var  integer
	 */
	private $umask;

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass(): void
	{
		if (!\defined('JPATH_ROOT'))
		{
			self::markTestSkipped('Constant `JPATH_ROOT` is not defined.');
		}
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		$this->umask    = umask(0);
		$this->testPath = sys_get_temp_dir() . '/' . microtime(true) . '.' . mt_rand();

		mkdir($this->testPath, 0777, true);

		$this->testPath = realpath($this->testPath);
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown(): void
	{
		Folder::delete($this->testPath);

		umask($this->umask);
	}

	/**
	 * Skip a test if unable to perform chmod
	 *
	 * @return void
	 */
	protected function skipIfUnableToChmod()
	{
		if (DIRECTORY_SEPARATOR === '\\')
		{
			$this->markTestSkipped('chmod is not supported on Windows');
		}
	}
}

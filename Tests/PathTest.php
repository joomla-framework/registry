<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\Exception\FilesystemException;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;

/**
 * Tests for the Joomla\Filesystem\Path class.
 */
class PathTest extends FilesystemTestCase
{
	/**
	 * Test canChmod method.
	 */
	public function testCanChmodFile()
	{
		$this->skipIfUnableToChmod();

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			Path::canChmod($this->testPath . '/' . $name)
		);
	}

	/**
	 * Test canChmod method.
	 */
	public function testCanChmodFolder()
	{
		$this->skipIfUnableToChmod();

		$this->assertTrue(
			Path::canChmod($this->testPath)
		);
	}

	/**
	 * Test canChmod method.
	 */
	public function testCanChmodNonExistingFile()
	{
		$this->skipIfUnableToChmod();

		$this->assertFalse(
			Path::canChmod($this->testPath . '/tempFile')
		);
	}

	/**
	 * Test setPermissions method.
	 */
	public function testSetAndGetPermissionsFile()
	{
		$this->skipIfUnableToChmod();

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		// The parent test case sets umask(0) therefore we are creating files with 0666 permissions
		$this->assertSame(
			'rw-rw-rw-',
			Path::getPermissions($this->testPath . '/' . $name)
		);

		$this->assertTrue(
			Path::setPermissions($this->testPath . '/' . $name, '0644')
		);

		// PHP caches permissions lookups, clear it before continuing
		clearstatcache();

		$this->assertSame(
			'rw-r--r--',
			Path::getPermissions($this->testPath . '/' . $name)
		);
	}

	/**
	 * Test setPermissions method.
	 */
	public function testSetAndGetPermissionsFolder()
	{
		$this->skipIfUnableToChmod();

		// The parent test case sets umask(0) therefore we are creating folders with 0777 permissions
		$this->assertSame(
			'rwxrwxrwx',
			Path::getPermissions($this->testPath)
		);

		$this->assertTrue(
			Path::setPermissions($this->testPath, null, '0755')
		);

		// PHP caches permissions lookups, clear it before continuing
		clearstatcache();

		$this->assertSame(
			'rwxr-xr-x',
			Path::getPermissions($this->testPath)
		);
	}

	/**
	 * Test setPermissions method.
	 */
	public function testSetAndGetPermissionsFolderWithFiles()
	{
		$this->skipIfUnableToChmod();

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		// The parent test case sets umask(0) therefore we are creating files with 0666 permissions
		$this->assertSame(
			'rw-rw-rw-',
			Path::getPermissions($this->testPath . '/' . $name)
		);

		// The parent test case sets umask(0) therefore we are creating folders with 0777 permissions
		$this->assertSame(
			'rwxrwxrwx',
			Path::getPermissions($this->testPath)
		);

		$this->assertTrue(
			Path::setPermissions($this->testPath, '0644', '0755')
		);

		// PHP caches permissions lookups, clear it before continuing
		clearstatcache();

		$this->assertSame(
			'rw-r--r--',
			Path::getPermissions($this->testPath . '/' . $name)
		);

		$this->assertSame(
			'rwxr-xr-x',
			Path::getPermissions($this->testPath)
		);
	}

	/**
	 * Test data for check method.
	 *
	 * @return  \Generator
	 */
	public function dataCheckValidPaths(): \Generator
	{
		yield ['/var/foo'];
		yield ['/var/foo/bar'];
		yield ['/var/fo.o/bar'];
		yield ['/var/./bar'];
	}

	/**
	 * Test checkValidPaths method.
	 *
	 * @param   string  $data  Path to check for valid
	 *
	 * @dataProvider dataCheckValidPaths
	 */
	public function testCheckValidPaths($data)
	{
		if (DIRECTORY_SEPARATOR === '\\')
		{
			$this->markTestSkipped('Checking paths is not supported on Windows');
		}

		$this->assertEquals(
			Path::clean(__DIR__ . $data),
			Path::check(__DIR__ . $data)
		);
	}

	/**
	 * Test data for check method exception.
	 *
	 * @return  \Generator
	 */
	public function dataCheckExceptionPaths(): \Generator
	{
		yield ['../var/foo/bar'];
		yield ['/var/../foo/bar'];
		yield ['/var/foo../bar'];
		yield ['/var/foo/..'];
		yield ['/var/foo..bar'];
		yield ['/var/foo/..bar'];
		yield ['/var/foo/bar..'];
		yield ['/var/..foo./bar'];
	}

	/**
	 * Test exceptions in check method.
	 *
	 * @param   string  $data  Paths to check.
	 *
	 * @dataProvider dataCheckExceptionPaths
	 */
	public function testCheckExceptionPaths($data)
	{
		$this->expectException(FilesystemException::class);

		Path::check(__DIR__ . $data);
	}

	/**
	 * Data provider for testClean() method.
	 *
	 * @return  \Generator
	 */
	public function getCleanData(): \Generator
	{
		yield 'Nothing to do.' => ['/var/www/foo/bar/baz', '/', '/var/www/foo/bar/baz'];
		yield 'One backslash.' => ['/var/www/foo\\bar/baz', '/', '/var/www/foo/bar/baz'];
		yield 'Two and one backslashes.' => ['/var/www\\\\foo\\bar/baz', '/', '/var/www/foo/bar/baz'];
		yield 'Mixed backslashes and double forward slashes.' => ['/var\\/www//foo\\bar/baz', '/', '/var/www/foo/bar/baz'];
		yield 'UNC path.' => ['\\\\www\\docroot', '\\', '\\\\www\\docroot'];
		yield 'UNC path with forward slash.' => ['\\\\www/docroot', '\\', '\\\\www\\docroot'];
		yield 'UNC path with UNIX directory separator.' => ['\\\\www/docroot', '/', '/www/docroot'];
	}

	/**
	 * Tests the clean method.
	 *
	 * @param   string  $input     Input Path
	 * @param   string  $ds        Directory Separator
	 * @param   string  $expected  Expected Output
	 *
	 * @dataProvider  getCleanData
	 */
	public function testClean($input, $ds, $expected)
	{
		$this->assertEquals(
			$expected,
			Path::clean($input, $ds)
		);
	}

	/**
	 * Tests the clean method with an array as an input.
	 */
	public function testCleanArrayPath()
	{
		$this->expectException(\InvalidArgumentException::class);

		Path::clean(array('/path/to/folder'));
	}

	/**
	 * Test isOwner method.
	 */
	public function testIsOwner()
	{
		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			Path::isOwner($this->testPath . '/' . $name)
		);
	}

	/**
	 * Test find method.
	 */
	public function testFind()
	{
		$this->assertFalse(
			Path::find(dirname(__DIR__), 'PathTest.php')
		);

		$this->assertEquals(
			__FILE__,
			Path::find(__DIR__, 'PathTest.php')
		);
	}

	/**
	 * Test resolve method
	 *
	 * @param   string  $path            test path
	 * @param   string  $expectedResult  expected path
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 *
	 * @dataProvider  getResolveData
	 */
	public function testResolve($path, $expectedResult)
	{
		$this->assertEquals(str_replace("_DS_", DIRECTORY_SEPARATOR, $expectedResult), Path::resolve($path));
	}

	/**
	 * Test resolve method
	 *
	 * @param   string  $path            test path
	 *
	 * @return void
	 *
	 * @since   1.4.0
	 *
	 * @dataProvider  getResolveExceptionData
	 */
	public function testResolveThrowsExceptionIfRootIsLeft($path)
	{
		$this->expectException(FilesystemException::class);
		$this->expectExceptionMessage('Path is outside of the defined root');
		Path::resolve($path);
	}

	/**
	 * Data provider for testResolve() method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getResolveData()
	{
		return array(
			array("/", "_DS_"),
			array("a", "a"),
			array("/test/", "_DS_test"),
			array("C:/", "C:"),
			array("/var/www/joomla", "_DS_var_DS_www_DS_joomla"),
			array("C:/iis/www/joomla", "C:_DS_iis_DS_www_DS_joomla"),
			array("var/www/joomla", "var_DS_www_DS_joomla"),
			array("./var/www/joomla", "var_DS_www_DS_joomla"),
			array("/var/www/foo/../joomla", "_DS_var_DS_www_DS_joomla"),
			array("C:/var/www/foo/../joomla", "C:_DS_var_DS_www_DS_joomla"),
			array("/var/www/../foo/../joomla", "_DS_var_DS_joomla"),
			array("C:/var/www/..foo../joomla", "C:_DS_var_DS_www_DS_..foo.._DS_joomla"),
			array("c:/var/www/..foo../joomla", "c:_DS_var_DS_www_DS_..foo.._DS_joomla"),
			array("/var/www///joomla", "_DS_var_DS_www_DS_joomla"),
			array("/var///www///joomla", "_DS_var_DS_www_DS_joomla"),
			array("C:/var///www///joomla", "C:_DS_var_DS_www_DS_joomla"),
			array("/var/\/../www///joomla", "_DS_www_DS_joomla"),
			array("C:/var///www///joomla", "C:_DS_var_DS_www_DS_joomla"),
			array("/var\\www///joomla", "_DS_var_DS_www_DS_joomla")
		);
	}

	/**
	 * Data provider for testResolve() method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getResolveExceptionData()
	{
		return array(
			array("../var/www/joomla"),
			array("/var/../../../www/joomla")
		);
	}
}

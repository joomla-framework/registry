<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session\Tests\Handler;

use Joomla\Session\Handler\FilesystemHandler;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Session\Handler\FilesystemHandler.
 */
class FilesystemHandlerTest extends TestCase
{
	/**
	 * {@inheritdoc}
	 */
	public static function setUpBeforeClass(): void
	{
		// Make sure the handler is supported in this environment
		if (!FilesystemHandler::isSupported())
		{
			static::markTestSkipped('The FilesystemHandler is unsupported in this environment.');
		}
	}

	/**
	 * @covers  Joomla\Session\Handler\FilesystemHandler
	 */
	public function testTheHandlerIsSupported()
	{
		$this->assertTrue(FilesystemHandler::isSupported());
	}

	/**
	 * @covers  Joomla\Session\Handler\FilesystemHandler
	 */
	public function testTheHandlerHandlesAnInvalidPath()
	{
		$this->expectException(\InvalidArgumentException::class);

		new FilesystemHandler('totally;invalid;string;for;this;object');
	}

	/**
	 * @covers  Joomla\Session\Handler\FilesystemHandler
	 */
	public function testTheHandlerIsInstantiatedCorrectly()
	{
		$phpSessionPath = ini_get('session.save_path');

		if (empty($phpSessionPath))
		{
			$this->expectException('\InvalidArgumentException');
		}

		$handler = new FilesystemHandler;

		$this->assertSame('files', ini_get('session.save_handler'));
	}

	/**
	 * @covers  Joomla\Session\Handler\FilesystemHandler
	 */
	public function testTheHandlerIsInstantiatedCorrectlyAndCreatesTheSavePathIfNeeded()
	{
		$handler = new FilesystemHandler(__DIR__ . '/savepath');

		// Temporarily skip this assertion due to changes in PHP 7.2
		// $this->assertSame(__DIR__ . '/savepath', ini_get('session.save_path'));
		$this->assertTrue(is_dir(realpath(__DIR__ . '/savepath')));

		rmdir(__DIR__ . '/savepath');
	}

	/**
	 * @param   string  $savePath          The path to inject into the handler
	 * @param   string  $expectedSavePath  The expected save path in the PHP configuration
	 * @param   string  $path              The expected filesystem path for the handler
	 *
	 * @covers  Joomla\Session\Handler\FilesystemHandler
	 *
	 * @dataProvider  savePathDataProvider
	 */
	public function testTheHandlerIsInstantiatedCorrectlyAndHandlesAllParametersAsExpected($savePath, $expectedSavePath, $path)
	{
		$handler = new FilesystemHandler($savePath);

		// Temporarily skip this assertion due to changes in PHP 7.2
		// $this->assertEquals($expectedSavePath, ini_get('session.save_path'));
		$this->assertTrue(is_dir(realpath($path)));

		rmdir($path);
	}

	/**
	 * Data provider with expected paths for handler construction
	 *
	 * @return  \Generator
	 */
	public function savePathDataProvider(): \Generator
	{
		$base = sys_get_temp_dir();

		yield ["$base/savepath", "$base/savepath", "$base/savepath"];
		yield ["5;$base/savepath", "5;$base/savepath", "$base/savepath"];
		yield ["5;0600;$base/savepath", "5;0600;$base/savepath", "$base/savepath"];
	}
}

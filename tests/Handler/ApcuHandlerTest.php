<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session\Tests\Handler;

use Joomla\Session\Handler\ApcuHandler;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Session\Handler\ApcuHandler.
 */
class ApcuHandlerTest extends TestCase
{
	/**
	 * ApcuHandler for testing
	 *
	 * @var  ApcuHandler
	 */
	private $handler;

	/**
	 * Options to inject into the handler
	 *
	 * @var  array
	 */
	private $options = ['prefix' => 'jfwtest_'];

	/**
	 * {@inheritdoc}
	 */
	public static function setUpBeforeClass(): void
	{
		// Make sure the handler is supported in this environment
		if (!ApcuHandler::isSupported())
		{
			static::markTestSkipped('The ApcuHandler is unsupported in this environment.');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->handler = new ApcuHandler($this->options);
	}

	/**
	 * @covers  Joomla\Session\Handler\ApcuHandler
	 */
	public function testTheHandlerIsSupported()
	{
		$this->assertTrue(ApcuHandler::isSupported());
	}

	/**
	 * @covers  Joomla\Session\Handler\ApcuHandler
	 */
	public function testTheHandlerOpensTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->open('foo', 'bar'));
	}

	/**
	 * @covers  Joomla\Session\Handler\ApcuHandler
	 */
	public function testTheHandlerClosesTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->close());
	}

	/**
	 * @covers  Joomla\Session\Handler\ApcuHandler
	 */
	public function testTheHandlerReadsDataFromTheSessionCorrectly()
	{
		$this->assertSame('', $this->handler->read('id'));
	}

	/**
	 * @covers  Joomla\Session\Handler\ApcuHandler
	 */
	public function testTheHandlerWritesDataToTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->write('id', 'data'));
	}

	/**
	 * @covers  Joomla\Session\Handler\ApcuHandler
	 */
	public function testTheHandlerDestroysTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->destroy('id'));
	}

	/**
	 * @covers  Joomla\Session\Handler\ApcuHandler
	 */
	public function testTheHandlerGarbageCollectsTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->gc(60));
	}
}

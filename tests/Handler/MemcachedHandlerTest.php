<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session\Tests\Handler;

use Joomla\Session\Handler\MemcachedHandler;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Session\Handler\MemcachedHandler.
 */
class MemcachedHandlerTest extends TestCase
{
	/**
	 * MemcachedHandler for testing
	 *
	 * @var  MemcachedHandler
	 */
	private $handler;

	/**
	 * Memcached object for testing
	 *
	 * @var  \Memcached
	 */
	private $memcached;

	/**
	 * Options to inject into the handler
	 *
	 * @var  array
	 */
	private $options = ['prefix' => 'jfwtest_', 'ttl' => 1000];

	/**
	 * {@inheritdoc}
	 */
	protected function setUp(): void
	{
		// Make sure the handler is supported in this environment
		if (!MemcachedHandler::isSupported())
		{
			static::markTestSkipped('The MemcachedHandler is unsupported in this environment.');
		}

		parent::setUp();

		// Parse the DSN details for the test server
		$dsn = defined('JTEST_MEMCACHED_DSN') ? JTEST_MEMCACHED_DSN : getenv('JTEST_MEMCACHED_DSN');

		if ($dsn)
		{
			// First let's trim the memcached: part off the front of the DSN if it exists.
			if (strpos($dsn, 'memcached:') === 0)
			{
				$dsn = substr($dsn, 10);
			}

			$options = [];

			// Split the DSN into its parts over semicolons.
			$parts = explode(';', $dsn);

			// Parse each part and populate the options array.
			foreach ($parts as $part)
			{
				list ($k, $v) = explode('=', $part, 2);
				$options[$k] = $v;
			}
		}
		else
		{
			$this->markTestSkipped('No configuration for Redis given');
		}

		$this->memcached = new \Memcached;
		$this->memcached->setOption(\Memcached::OPT_COMPRESSION, false);
		$this->memcached->addServer($options['host'], $options['port']);

		if (@fsockopen($options['host'], $options['port']) === false)
		{
			unset($this->memcached);
			$this->markTestSkipped('Cannot connect to Memcached.');
		}

		$this->handler = new MemcachedHandler($this->memcached, $this->options);
	}

	/**
	 * @covers  Joomla\Session\Handler\MemcachedHandler
	 */
	public function testTheHandlerIsSupported()
	{
		$this->assertTrue(MemcachedHandler::isSupported());
	}

	/**
	 * @covers  Joomla\Session\Handler\MemcachedHandler
	 */
	public function testTheHandlerOpensTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->open('foo', 'bar'));
	}

	/**
	 * @covers  Joomla\Session\Handler\MemcachedHandler
	 */
	public function testTheHandlerClosesTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->close());
	}

	/**
	 * @covers  Joomla\Session\Handler\MemcachedHandler
	 */
	public function testTheHandlerReadsDataFromTheSessionCorrectly()
	{
		$this->handler->write('id', 'foo');

		$this->assertSame('foo', $this->handler->read('id'));
	}

	/**
	 * @covers  Joomla\Session\Handler\MemcachedHandler
	 */
	public function testTheHandlerWritesDataToTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->write('id', 'data'));
	}

	/**
	 * @covers  Joomla\Session\Handler\MemcachedHandler
	 */
	public function testTheHandlerDestroysTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->destroy('id'));
	}

	/**
	 * @covers  Joomla\Session\Handler\MemcachedHandler
	 */
	public function testTheHandlerGarbageCollectsTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->gc(60));
	}
}

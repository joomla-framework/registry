<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session\Tests\Handler;

use Joomla\Session\Handler\RedisHandler;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Session\Handler\RedisHandler.
 */
class RedisHandlerTest extends TestCase
{
	/**
	 * RedisHandler for testing
	 *
	 * @var  RedisHandler
	 */
	private $handler;

	/**
	 * Mock Redis object for testing
	 *
	 * @var  \Redis
	 */
	private $redis;

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
		if (!RedisHandler::isSupported())
		{
			static::markTestSkipped('The RedisHandler is unsupported in this environment.');
		}

		parent::setUp();

		// Parse the DSN details for the test server
		$dsn = defined('JTEST_REDIS_DSN') ? JTEST_REDIS_DSN : getenv('JTEST_REDIS_DSN');

		if ($dsn)
		{
			// First let's trim the redis: part off the front of the DSN if it exists.
			if (strpos($dsn, 'redis:') === 0)
			{
				$dsn = substr($dsn, 6);
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

		$this->redis = new \Redis();

		if (!$this->redis->connect($options['host'], $options['port']))
		{
			unset($this->redis);
			$this->markTestSkipped('Cannot connect to Redis.');
		}

		$this->handler = new RedisHandler($this->redis, $this->options);
	}

	/**
	 * @covers  Joomla\Session\Handler\RedisHandler
	 */
	public function testTheHandlerIsSupported()
	{
		$this->assertSame(
			(extension_loaded('redis') && class_exists('Redis')),
			RedisHandler::isSupported()
		);
	}

	/**
	 * @covers  Joomla\Session\Handler\RedisHandler
	 */
	public function testTheHandlerOpensTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->open('foo', 'bar'));
	}

	/**
	 * @covers  Joomla\Session\Handler\RedisHandler
	 */
	public function testTheHandlerClosesTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->close());
	}

	/**
	 * @covers  Joomla\Session\Handler\RedisHandler
	 */
	public function testTheHandlerReadsDataFromTheSessionCorrectly()
	{
		$this->handler->write('id', 'foo');

		$this->assertSame('foo', $this->handler->read('id'));
	}

	/**
	 * @covers  Joomla\Session\Handler\RedisHandler
	 */
	public function testTheHandlerWritesDataToTheSessionCorrectlyWithATimeToLive()
	{
		$this->assertTrue($this->handler->write('id', 'data'));
	}

	/**
	 * @covers  Joomla\Session\Handler\RedisHandler
	 */
	public function testTheHandlerWritesDataToTheSessionCorrectlyWithoutATimeToLive()
	{
		$handler = new RedisHandler($this->redis, ['prefix' => 'jfwtest_', 'ttl' => 0]);

		$this->assertTrue($handler->write('id', 'data'));
	}

	/**
	 * @covers  Joomla\Session\Handler\RedisHandler
	 */
	public function testTheHandlerDestroysTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->destroy('id'));
	}

	/**
	 * @covers  Joomla\Session\Handler\RedisHandler
	 */
	public function testTheHandlerGarbageCollectsTheSessionCorrectly()
	{
		$this->assertTrue($this->handler->gc(60));
	}
}

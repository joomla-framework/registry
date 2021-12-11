<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session\Tests\Handler;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Sqlite\SqliteDriver;
use Joomla\Session\Handler\DatabaseHandler;
use Joomla\Session\Tests\DatabaseManager;
use Joomla\Test\DatabaseManager as BaseDatabaseManager;
use Joomla\Test\DatabaseTestCase;

/**
 * Test class for Joomla\Session\Handler\DatabaseHandler.
 */
class DatabaseHandlerTest extends DatabaseTestCase
{
	/**
	 * DatabaseHandler for testing
	 *
	 * @var  DatabaseHandler
	 */
	private $handler;

	/**
	 * Flag if the session table has been created
	 *
	 * @var  boolean
	 */
	private static $sessionTableCreated = false;

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass(): void
	{
		// Make sure the driver is supported
		if (!SqliteDriver::isSupported())
		{
			static::markTestSkipped('The SQLite driver is not supported on this platform.');
		}

		parent::setUpBeforeClass();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->handler = new DatabaseHandler(static::$connection);

		// Make sure our session table is present
		if (!self::$sessionTableCreated)
		{
			$this->handler->createDatabaseTable();

			self::$sessionTableCreated = true;
		}
	}

	/**
	 * Create the database manager for this test class.
	 *
	 * If necessary, this method can be extended to create your own subclass of the base DatabaseManager object to customise
	 * the behaviors in your application.
	 *
	 * @return  DatabaseManager
	 */
	protected static function createDatabaseManager(): BaseDatabaseManager
	{
		return new DatabaseManager;
	}

	/**
	 * @covers  Joomla\Session\Handler\DatabaseHandler
	 */
	public function testTheHandlerIsSupported()
	{
		$this->assertTrue(DatabaseHandler::isSupported());
	}

	/**
	 * @covers  Joomla\Session\Handler\DatabaseHandler
	 */
	public function testValidateSessionDataIsCorrectlyReadWrittenAndDestroyed()
	{
		$sessionData = ['foo' => 'bar', 'joomla' => 'rocks'];
		$sessionId   = 'sid';

		$this->assertTrue($this->handler->open('', $sessionId));
		$this->assertTrue($this->handler->write($sessionId, json_encode(['foo' => 'bar'])));
		$this->assertTrue($this->handler->write($sessionId, json_encode($sessionData)));
		$this->assertSame($sessionData, json_decode($this->handler->read($sessionId), true));
		$this->assertTrue($this->handler->destroy($sessionId));
		$this->assertTrue($this->handler->gc(900));
		$this->assertTrue($this->handler->close());
	}
}

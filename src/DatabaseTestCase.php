<?php
/**
 * Part of the Joomla Framework Test Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Test;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\Exception\ConnectionFailureException;
use Joomla\Test\Exception\MissingDatabaseCredentials;
use PHPUnit\Framework\TestCase;

/**
 * Base test case for tests interacting with a database
 *
 * @since  2.0.0
 */
abstract class DatabaseTestCase extends TestCase
{
	/**
	 * The database connection for the test case
	 *
	 * @var    DatabaseInterface|null
	 * @since  2.0.0
	 */
	protected static $connection;

	/**
	 * The database manager
	 *
	 * @var    DatabaseManager|null
	 * @since  2.0.0
	 */
	protected static $dbManager;

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public static function setUpBeforeClass(): void
	{
		try
		{
			$manager = static::getDatabaseManager();

			$connection = $manager->getConnection();
			$manager->dropDatabase();
			$manager->createDatabase();
			$connection->select($manager->getDbName());

			static::$connection = $connection;
		}
		catch (MissingDatabaseCredentials $exception)
		{
			static::markTestSkipped('Database credentials are not set, cannot run database tests.');
		}
		catch (ConnectionFailureException $exception)
		{
			static::markTestSkipped('Could not connect to the test database, cannot run database tests.');
		}
	}

	/**
	 * This method is called after the last test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public static function tearDownAfterClass(): void
	{
		if (static::$connection !== null)
		{
			static::getDatabaseManager()->dropDatabase();
			static::$connection->disconnect();
		}
	}

	/**
	 * Create the database manager for this test class.
	 *
	 * If necessary, this method can be extended to create your own subclass of the base DatabaseManager object to customise
	 * the behaviors in your application.
	 *
	 * @return  DatabaseManager
	 *
	 * @since   2.0.0
	 */
	protected static function createDatabaseManager(): DatabaseManager
	{
		return new DatabaseManager;
	}

	/**
	 * Fetch the database manager for this test case.
	 *
	 * This creates a singleton manager used for all tests in this class.
	 *
	 * @return  DatabaseManager
	 *
	 * @since   2.0.0
	 */
	protected static function getDatabaseManager(): DatabaseManager
	{
		if (!static::$dbManager)
		{
			static::$dbManager = static::createDatabaseManager();
		}

		return static::$dbManager;
	}
}

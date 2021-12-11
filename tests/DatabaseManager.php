<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session\Tests;

use Joomla\Test\DatabaseManager as BaseDatabaseManager;

/**
 * Extended database manager to handle configuring the package's test database
 */
class DatabaseManager extends BaseDatabaseManager
{
	/**
	 * Initialize the parameter storage for the database connection
	 *
	 * Overrides the behavior of the parent class to force an in-memory SQLite database for testing
	 *
	 * @return  void
	 */
	protected function initialiseParams(): void
	{
		if (empty($this->params))
		{
			$this->params = [
				'driver'   => 'sqlite',
				'user'     => null,
				'database' => ':memory:',
				'select'   => false,
			];
		}
	}
}

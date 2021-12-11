<?php
/**
 * Part of the Joomla Framework Model Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Model;

use Joomla\Database\DatabaseInterface;

/**
 * Trait representing a model holding a database reference
 *
 * @since  1.3.0
 */
trait DatabaseModelTrait
{
	/**
	 * The database driver.
	 *
	 * @var    DatabaseInterface
	 * @since  1.3.0
	 */
	protected $db;

	/**
	 * Get the database driver.
	 *
	 * @return  DatabaseInterface  The database driver.
	 *
	 * @since   1.3.0
	 * @throws  \UnexpectedValueException
	 */
	public function getDb()
	{
		if ($this->db)
		{
			return $this->db;
		}

		throw new \UnexpectedValueException('Database driver not set in ' . __CLASS__);
	}

	/**
	 * Set the database driver.
	 *
	 * @param   DatabaseInterface  $db  The database driver.
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 */
	public function setDb(DatabaseInterface $db)
	{
		$this->db = $db;
	}
}

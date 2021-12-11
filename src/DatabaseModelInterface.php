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
 * Joomla Framework Database Model Interface
 *
 * @since  1.3.0
 */
interface DatabaseModelInterface
{
	/**
	 * Get the database driver.
	 *
	 * @return  DatabaseInterface  The database driver.
	 *
	 * @since   1.3.0
	 */
	public function getDb();
}

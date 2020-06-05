<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Data\Tests\Stubs;

use Joomla\Data\DataObject;

/**
 * Derived Data\DataObject class for testing.
 */
class Buran extends DataObject
{
	public $rocket = false;

	/**
	 * Method to set the test_value.
	 *
	 * @param   string  $value  The test value.
	 *
	 * @return  $this
	 */
	protected function setTestValue($value)
	{
		// Set the property as uppercase.
		return strtoupper($value);
	}
}

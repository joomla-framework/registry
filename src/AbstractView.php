<?php
/**
 * Part of the Joomla Framework View Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\View;

/**
 * Joomla Framework Abstract View Class
 *
 * @since  1.0
 */
abstract class AbstractView implements ViewInterface
{
	/**
	 * The data array to pass to the renderer
	 *
	 * @var    array
	 * @since  2.0.0
	 */
	private $data = [];

	/**
	 * Adds an object to the data array
	 *
	 * @param   string  $key    The array key
	 * @param   mixed   $value  The data value to add
	 *
	 * @return  $this
	 *
	 * @since   2.0.0
	 */
	public function addData(string $key, $value)
	{
		$this->data[$key] = $value;

		return $this;
	}

	/**
	 * Resets the internal data array
	 *
	 * @return  $this
	 *
	 * @since   2.0.0
	 */
	public function clearData()
	{
		$this->data = [];

		return $this;
	}

	/**
	 * Retrieves the data array
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * Removes an object to the data array
	 *
	 * @param   string  $key  The array key to remove
	 *
	 * @return  $this
	 *
	 * @since   2.0.0
	 */
	public function removeData(string $key)
	{
		unset($this->data[$key]);

		return $this;
	}

	/**
	 * Sets additional data to the data array
	 *
	 * @param   array  $data  Data to merge into the existing data array
	 *
	 * @return  $this
	 *
	 * @since   2.0.0
	 */
	public function setData(array $data)
	{
		$this->data = array_merge($this->data, $data);

		return $this;
	}
}

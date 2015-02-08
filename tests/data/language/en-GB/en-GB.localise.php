<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\Language\Localise\AbstractLocalise;

/**
 * en-GB localise class
 *
 * @since  1.0
 */
class En_GBLocalise extends AbstractLocalise
{
	/**
	 * Returns the potential suffixes for a specific number of items
	 *
	 * @param   integer  $count  The number of items.
	 *
	 * @return  array  An array of potential suffixes.
	 *
	 * @since   1.0
	 */
	public function getPluralSuffixes($count)
	{
		if ($count == 0)
		{
			$return = array('0');
		}
		elseif ($count == 1)
		{
			$return = array('1');
		}
		else
		{
			$return = array('MORE');
		}

		return $return;
	}

	/**
	 * Returns the ignored search words
	 *
	 * @return  array  An array of ignored search words.
	 *
	 * @since   1.0
	 */
	public function getIgnoredSearchWords()
	{
		$search_ignore = array();
		$search_ignore[] = "and";
		$search_ignore[] = "in";
		$search_ignore[] = "on";

		return $search_ignore;
	}

	/**
	 * Custom translitrate fucntion to use.
	 *
	 * @param   string  $string  String to transliterate
	 *
	 * @return  integer  The number of chars to display when searching.
	 *
	 * @since   1.0
	 */
	public function transliterate($string)
	{
		return str_replace(
			array('a', 'c', 'e', 'g'),
			array('b', 'd', 'f', 'h'),
			$string
		);
	}
}

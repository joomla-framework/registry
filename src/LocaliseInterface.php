<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Joomla Framework Language Interface
 *
 * @since  __DEPLOY_VERSION__
 */
interface LocaliseInterface
{
	/**
	 * Transliterate function
	 *
	 * This method processes a string and replaces all accented UTF-8 characters by unaccented
	 * ASCII-7 "equivalents".
	 *
	 * @param   string  $string  The string to transliterate.
	 *
	 * @return  string|boolean  The transliterated string or boolean false on a failure
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function transliterate($string);

	/**
	 * Returns an array of suffixes for plural rules.
	 *
	 * @param   integer  $count  The count number the rule is for.
	 *
	 * @return  string[]  The array of suffixes.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getPluralSuffixes($count);
}

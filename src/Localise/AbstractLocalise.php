<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Localise;

use Joomla\Language\LocaliseInterface;
use Joomla\Language\Transliterate;
use Joomla\String\String;

/**
 * Abstract localisation handler class
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractLocalise implements LocaliseInterface
{
	/**
	 * Transliterate function
	 *
	 * This method processes a string and replaces all accented UTF-8 characters by unaccented
	 * ASCII-7 "equivalents".
	 *
	 * @param   string  $string  The string to transliterate.
	 *
	 * @return  string  The transliteration of the string.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function transliterate($string)
	{
		$transliterate = new Transliterate;
		$string = $transliterate->utf8_latin_to_ascii($string);

		return String::strtolower($string);
	}

	/**
	 * Returns an array of suffixes for plural rules.
	 *
	 * @param   integer  $count  The count number the rule is for.
	 *
	 * @return  array    The array of suffixes.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getPluralSuffixes($count)
	{
		return array((string) $count);
	}

	/**
	 * Returns an array of ignored search words
	 *
	 * @return  array  The array of ignored search words.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getIgnoredSearchWords()
	{
		return array();
	}

	/**
	 * Returns a lower limit integer for length of search words
	 *
	 * @return  integer  The lower limit integer for length of search words.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getLowerLimitSearchWord()
	{
		return 3;
	}

	/**
	 * Returns an upper limit integer for length of search words
	 *
	 * @return  integer  The upper limit integer for length of search words.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getUpperLimitSearchWord()
	{
		return 20;
	}

	/**
	 * Returns the number of characters displayed in search results.
	 *
	 * @return  integer  The number of characters displayed.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getSearchDisplayedCharactersNumber()
	{
		return 200;
	}
}

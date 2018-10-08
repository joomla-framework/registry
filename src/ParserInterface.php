<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Interface describing a language file loader
 *
 * @since  __DEPLOY_VERSION__
 */
interface ParserInterface
{
	/**
	 * Get the type of loader
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getType(): string;

	/**
	 * Load the strings from a file
	 *
	 * @param   string  $filename  The name of the file.
	 *
	 * @return  string[]
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException on a load/parse error
	 */
	public function loadFile(string $filename): array;
}

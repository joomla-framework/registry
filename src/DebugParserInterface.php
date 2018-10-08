<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Interface describing a language file parser capable of debugging a file
 *
 * @since  __DEPLOY_VERSION__
 */
interface DebugParserInterface extends ParserInterface
{
	/**
	 * Parse a file and check its contents for valid structure
	 *
	 * @param   string  $filename  The name of the file.
	 *
	 * @return  string[]  Array containing a list of errors
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function debugFile(string $filename): array;
}

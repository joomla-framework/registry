<?php
/**
 * Part of the Joomla Framework Archive Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Exception;

/**
 * Exception class defining an unknown archive type
 *
 * @since  2.0.0
 */
class UnknownArchiveException extends \InvalidArgumentException
{
	/**
	 * The file type that couldn't be matched to a parser
	 *
	 * @type   string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $fileType = '';

	/**
	 * Constructor
	 *
	 * @param   string      $fileType  The Exception message to throw.
	 * @param   string      $message   The Exception message to throw.
	 * @param   int         $code      The Exception code.
	 * @param   \Throwable  $previous  The previous throwable used for the exception chaining.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function __construct($fileType, $message = '', $code = 0, \Throwable $previous = null)
	{
		$this->fileType = $fileType;

		parent::__construct($message, $code, $previous);
	}

	public function getUnknownFileType(): string
	{
		return $this->fileType;
	}
}

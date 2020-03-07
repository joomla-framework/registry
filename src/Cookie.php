<?php
/**
 * Part of the Joomla Framework Input Package
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Input;

/**
 * Joomla! Input Cookie Class
 *
 * @since  1.0
 */
class Cookie extends Input
{
	/**
	 * Constructor.
	 *
	 * @param   array  $source   Source data (Optional, default is $_COOKIE)
	 * @param   array  $options  Array of configuration parameters (Optional)
	 *
	 * @since   1.0
	 */
	public function __construct($source = null, array $options = [])
	{
		if (empty($source))
		{
			$source = $_COOKIE;
		}

		parent::__construct($source, $options);
	}

	/**
	 * Sets a value
	 *
	 * @param   string   $name      Name of the value to set.
	 * @param   mixed    $value     Value to assign to the input.
	 * @param   array    $options   An associative array which may have any of the keys expires, path, domain,
	 *                              secure, httponly and samesite. The values have the same meaning as described
	 *                              for the parameters with the same name. The value of the samesite element
	 *                              should be either Lax or Strict. If any of the allowed options are not given,
	 *                              their default values are the same as the default values of the explicit
	 *                              parameters. If the samesite element is omitted, no SameSite cookie attribute
	 *                              is set.
	 *
	 * @return  void
	 *
	 * @link    https://www.ietf.org/rfc/rfc2109.txt
	 * @link    https://php.net/manual/en/function.setcookie.php
	 *
	 * @since   1.0
	 *
	 * @note    As of 1.4.0, the (name, value, expire, path, domain, secure, httpOnly) signature is deprecated and will not be supported
	 *          when support for PHP 7.2 and earlier is dropped
	 */
	public function set($name, $value, $options = [])
	{
		// BC layer to convert old method parameters.
		if (is_array($options) === false)
		{
			@trigger_error(
				sprintf(
					'The %1$s($name, $value, $expire, $path, $domain, $secure, $httpOnly) signature is deprecated and will not be supported'
						. ' once support for PHP 7.2 and earlier is dropped, use the %1$s($name, $value, $options) signature instead.',
					__METHOD__
				),
				E_USER_DEPRECATED
			);

			$argList = func_get_args();

			$options = [
				'expires'  => $argList[2] ?? 0,
				'path'     => $argList[3] ?? '',
				'domain'   => $argList[4] ?? '',
				'secure'   => $argList[5] ?? false,
				'httponly' => $argList[6] ?? false,
			];
		}

		// Set the cookie
		if (version_compare(PHP_VERSION, '7.3', '>='))
		{
			setcookie($name, $value, $options);
		}
		else
		{
			// Using the setcookie function before php 7.3, make sure we have default values.
			if (array_key_exists('expires', $options) === false)
			{
				$options['expires'] = 0;
			}

			if (array_key_exists('path', $options) === false)
			{
				$options['path'] = '';
			}

			if (array_key_exists('domain', $options) === false)
			{
				$options['domain'] = '';
			}

			if (array_key_exists('secure', $options) === false)
			{
				$options['secure'] = false;
			}

			if (array_key_exists('httponly', $options) === false)
			{
				$options['httponly'] = false;
			}

			setcookie($name, $value, $options['expires'], $options['path'], $options['domain'], $options['secure'], $options['httponly']);
		}

		$this->data[$name] = $value;
	}
}

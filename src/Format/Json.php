<?php
/**
 * Part of the Joomla Framework Registry Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Format;

use Joomla\Registry\AbstractRegistryFormat;
use Joomla\String\String;
use Joomla\Utilities\JsonHelper;

/**
 * JSON format handler for Registry.
 *
 * @since  1.0
 */
class Json extends AbstractRegistryFormat
{
	/**
	 * Default set of options for JSON encoding
	 * @var     array
	 * @since   1.0
	 */

	protected $options = array(
		'hex_tag'=>false,
		'hex_amp'=>false,
		'hex_apos'=>false,
		'hex_quot'=>false,
		'force_object'=>false,
		'numeric_check'=>false,
		'pretty_print'=>false,
		'unescaped_slashes'=>false,
		'unescaped_unicode'=>false
	);
	/**
	 * Converts an object into a JSON formatted string.
	 *
	 * @param   object  $object   Data source object.
	 * @param   array   $options  Options used by the formatter.
	 *
	 * @return  string  JSON formatted string.
	 *
	 * @since   1.0
	 */
	public function objectToString($object, $options = array())
	{
	  $options = array_merge($this->options, (array) $options);
	  $encode_options = 0;

		//Options below are supported since PHP 5.3.0
		if ($options['hex_tag']) $encode_options |= JSON_HEX_TAG;
		if ($options['hex_amp']) $encode_options |= JSON_HEX_AMP;
		if ($options['hex_apos']) $encode_options |= JSON_HEX_APOS;
		if ($options['hex_quot']) $encode_options |= JSON_HEX_QUOT;
		if ($options['force_object']) $encode_options |= JSON_FORCE_OBJECT;
		if ($options['numeric_check']) $encode_options |= JSON_NUMERIC_CHECK;


		if (version_compare(PHP_VERSION, '5.4.0', '>='))
		{
			//Options below are supported since PHP 5.4.0
			if ($options['pretty_print']) $encode_options |= JSON_PRETTY_PRINT;
			if ($options['unescaped_slashes']) $encode_options |= JSON_UNESCAPED_SLASHES;
			if ($options['unescaped_unicode']) $encode_options |= JSON_UNESCAPED_UNICODE;
			
		}

		$result = json_encode($object, $encode_options);
		
		if (version_compare(PHP_VERSION, '5.4.0', '<')) 
		{
			if ($options['pretty_print'])
			{
				//Use 4 spaces to emulate JSON_PRETTY_PRINT behavior
				$result = JsonHelper::prettify($result, '    ');
			}
			if ($options['unescaped_unicode'])
			{
				$result = String::unicode_to_utf8($result);
			}
			if ($options['unescaped_slashes'])
			{
				$result = str_replace("\\/", "/", $result);
			}
		}

		return $result;

	}

	/**
	 * Parse a JSON formatted string and convert it into an object.
	 *
	 * If the string is not in JSON format, this method will attempt to parse it as INI format.
	 *
	 * @param   string  $data     JSON formatted string to convert.
	 * @param   array   $options  Options used by the formatter.
	 *
	 * @return  object   Data object.
	 *
	 * @since   1.0
	 */
	public function stringToObject($data, array $options = array('processSections' => false))
	{
		$data = trim($data);

		if ((substr($data, 0, 1) != '{') && (substr($data, -1, 1) != '}'))
		{
			$ini = AbstractRegistryFormat::getInstance('Ini');
			$obj = $ini->stringToObject($data, $options);
		}
		else
		{
			$obj = json_decode($data);
		}

		return $obj;
	}
	
}

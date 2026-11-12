<?php

/**
 * Part of the Joomla Framework Registry Package
 *
 * @copyright  Copyright (C) 2013 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Format;

use Joomla\Registry\Factory;
use Joomla\Registry\FormatInterface;

/**
 * JSON format handler for Registry.
 *
 * @since  1.0.0
 */
class Json implements FormatInterface
{
    /**
     * Converts an object into a JSON formatted string.
     *
     * @param  object  $object   Data source object.
     * @param  array   $options  Options used by the formatter.
     *
     * @return  string  JSON formatted string.
     *
     * @throws  \RuntimeException  if the object cannot be encoded
     *
     * @since   1.0.0
     * @since   __DEPLOY_VERSION__  Throws a `RuntimeException` if encoding fails instead of returning `false`.
     */
    public function objectToString($object, array $options = [])
    {
        $bitMask = $options['bitmask'] ?? 0;
        $depth   = $options['depth'] ?? 512;

        $string = \json_encode($object, $bitMask, $depth);

        if ($string === false) {
            throw new \RuntimeException(\sprintf('Error encoding JSON data: %s', \json_last_error_msg()));
        }

        return $string;
    }

    /**
     * Parse a JSON formatted string and convert it into an object.
     *
     * If the string is not in JSON format and does not start with "{", this method will attempt to parse it as INI
     * format. This fallback is deprecated; set the `iniFallback` option to `false` to throw an exception instead.
     *
     * @param  string  $data     JSON formatted string to convert.
     * @param  array   $options  Options used by the formatter.
     *
     * @return  object   Data object.
     *
     * @throws  \RuntimeException
     * @since   1.0.0
     * @since   __DEPLOY_VERSION__  The INI fallback is deprecated and can be disabled with the `iniFallback` option.
     */
    public function stringToObject(string $data, array $options = ['processSections' => false])
    {
        $data = \trim($data);

        if (empty($data)) {
            return new \stdClass();
        }

        $decoded = \json_decode($data);

        // Check for an error decoding the data
        if ($decoded === null && \json_last_error() !== JSON_ERROR_NONE) {
            // If it's an ini file, parse as ini.
            if ($data !== '' && $data[0] !== '{' && ($options['iniFallback'] ?? true)) {
                trigger_deprecation(
                    'joomla/registry',
                    '__DEPLOY_VERSION__',
                    'Parsing INI data with the JSON format is deprecated and will throw a RuntimeException in 5.0.'
                    . ' Load the data with the INI format instead, or set the "iniFallback" option to false.'
                );

                return Factory::getFormat('Ini')->stringToObject($data, $options);
            }

            throw new \RuntimeException(\sprintf('Error decoding JSON data: %s', \json_last_error_msg()));
        }

        return (object) $decoded;
    }
}

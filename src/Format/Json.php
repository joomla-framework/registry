<?php

/**
 * Part of the Joomla Framework Registry Package
 *
 * @copyright  Copyright (C) 2013 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Format;

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
     * @since   1.0.0
     */
    public function objectToString(object $object, array $options = []): string
    {
        $bitMask = $options['bitmask'] ?? 0;
        $depth   = $options['depth'] ?? 512;

        return \json_encode($object, $bitMask, $depth);
    }

    /**
     * Parse a JSON formatted string and convert it into an object.
     *
     * @param  string  $data     JSON formatted string to convert.
     * @param  array   $options  Options used by the formatter.
     *
     * @return  object   Data object.
     *
     * @throws  \RuntimeException  if the string is not valid JSON
     * @since   1.0.0
     * @since   __DEPLOY_VERSION__  A string that is not valid JSON is no longer parsed as INI, but throws an exception.
     */
    public function stringToObject(string $data, array $options = ['processSections' => false]): object
    {
        $data = \trim($data);

        if (empty($data)) {
            return new \stdClass();
        }

        $decoded = \json_decode($data);

        // Check for an error decoding the data
        if ($decoded === null && \json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(\sprintf('Error decoding JSON data: %s', \json_last_error_msg()));
        }

        return (object) $decoded;
    }
}

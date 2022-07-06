<?php

/**
 * Part of the Joomla Framework Registry Package
 *
 * @copyright  Copyright (C) 2015 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry;

/**
 * Interface defining a format object
 *
 * @since  1.5.0
 * @since  1.5.0  Deprecated `AbstractRegistryFormat` in favor of the `FormatInterface`.
 * @since  2.0.0  Deprecated `AbstractRegistryFormat`. Format objects must now implement the `FormatInterface`.
 */
interface FormatInterface
{
    /**
     * Converts an object into a formatted string.
     *
     * @param  object  $object   Data Source Object.
     * @param  array   $options  An array of options for the formatter.
     *
     * @return  string  Formatted string.
     *
     * @since   1.5.0
     * @since   2.0.0  The `FormatInterface::objectToString()` method typehints the `$options` argument as an array;
     *                 this was not enforced before 2.0.0 with `AbstractRegistryFormat::objectToString()`.
     */
    public function objectToString($object, array $options = []);

    /**
     * Converts a formatted string into an object.
     *
     * @param  string  $data     Formatted string
     * @param  array   $options  An array of options for the formatter.
     *
     * @return  object  Data Object
     *
     * @since   1.5.0
     */
    public function stringToObject($data, array $options = []);
}

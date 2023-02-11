<?php

/**
 * Part of the Joomla Framework Registry Package
 *
 * @copyright  Copyright (C) 2023 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry;

/**
 * Flat Registry class
 *
 * @since  2.1.0
 */
class FlatRegistry extends Registry
{
    /**
     * Check if a registry key exists.
     *
     * @param  string  $path  Registry path
     *
     * @return  boolean
     *
     * @since   2.1.0
     */
    public function exists($path)
    {
        if (empty($path)) {
            return false;
        }

        return isset($this->data->$path);
    }

    /**
     * Get a registry value.
     *
     * @param  string  $path     Registry path
     * @param  mixed   $default  Optional default value, returned if the internal value is null.
     *
     * @return  mixed  Value of entry or null
     *
     * @since   2.1.0
     */
    public function get($path, $default = null)
    {
        if (empty($path)) {
            return $default;
        }

        return (isset($this->data->$path) && $this->data->$path !== null && $this->data->$path !== '')
            ? $this->data->$path
            : $default;
    }

    /**
     * Set a registry value.
     *
     * @param  string  $path       Registry Path
     * @param  mixed   $value      Value of entry
     * @param  string  $separator  Ignored here
     *
     * @return  mixed  The value of the that has been set.
     *
     * @since   2.1.0
     */
    public function set($path, $value, $separator = null)
    {
        $result = $this->data->$path ?? null;

        $this->data->$path = $value;

        return $result;
    }

    /**
     * Append value to a path in registry if it is an array, or override otherwise.
     *
     * @param  string  $path   Parent registry Path
     * @param  mixed   $value  Value of entry
     *
     * @return  mixed  The value of the that has been set.
     *
     * @since   2.1.0
     */
    public function append($path, $value)
    {
        $prevValue = $this->get($path, []);

        if (is_array($prevValue)) {
            $prevValue[] = $value;
        } else {
            // We cannot append to non array
            $prevValue = $value;
        }

        return $this->set($path, $prevValue);
    }

    /**
     * Delete a registry value
     *
     * @param  string  $path  Registry Path
     *
     * @return  mixed  The value of the removed node or null if not set
     *
     * @since   2.1.0
     */
    public function remove($path)
    {
        $result = (isset($this->data->$path) && $this->data->$path !== null && $this->data->$path !== '')
            ? $this->data->$path
            : null;

        unset($this->data->$path);

        return $result;
    }

    /**
     * Load an associative array of values into the default namespace
     *
     * @param  array    $array      Associative array of value to load
     * @param  boolean  $flattened  Ignored here
     * @param  string   $separator  Ignored here
     *
     * @return  $this
     *
     * @since   2.1.0
     */
    public function loadArray(array $array, $flattened = false, $separator = null)
    {
        return parent::loadArray($array, true, null);
    }

    /**
     * Dump to one dimension array, have same behavior as toArray() method.
     *
     * @param  string  $separator  Ignored here
     *
     * @return  string[]  Dumped array.
     *
     * @since   2.1.0
     */
    public function flatten($separator = null)
    {
        return $this->toArray();
    }
}

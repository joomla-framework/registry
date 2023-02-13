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
class FlatRegistry implements RegistryInterface, \JsonSerializable
{
    /**
     * Data storage
     * @var array
     */
    private $data = [];

    /**
     * Check if a registry key exists.
     *
     * @param  string  $key  Registry key
     *
     * @return  boolean
     *
     * @since   2.1.0
     */
    public function exists(string $key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get a registry value.
     *
     * @param  string  $key      Registry key
     * @param  mixed   $default  Optional default value, returned if the internal value is null or empty string.
     *
     * @return  mixed  Value of entry or default value
     *
     * @since   2.1.0
     */
    public function get(string $key, $default = null)
    {
        $value = $this->data[$key] ?? null;

        return ($value !== null && $value !== '') ? $value : $default;
    }

    /**
     * Set a registry value.
     *
     * @param  string  $key    Registry key
     * @param  mixed   $value  Value of entry
     *
     * @return  mixed  Previous value for the key.
     *
     * @since   2.1.0
     */
    public function set(string $key, $value)
    {
        $prevValue = $this->data[$key] ?? null;

        $this->data[$key] = $value;

        return $prevValue;
    }

    /**
     * Delete a registry value.
     *
     * @param  string  $key  Registry key
     *
     * @return  mixed  The value of the removed node or null if not set
     *
     * @since   2.1.0
     */
    public function remove(string $key)
    {
        $prevValue = $this->data[$key] ?? null;

        unset($this->data[$key]);

        return $prevValue;
    }

    /**
     * Return data as array.
     *
     * @return  array
     *
     * @since   2.1.0
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Load data in to registry, will override an existing keys if exists.
     *
     * @param iterable $data  Data to load
     *
     * @return static
     *
     * @since   2.1.0
     */
    public function loadData(\Iterable $data)
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Implementation for the JsonSerializable interface.
     *
     * @return  array
     *
     * @since   2.1.0
     */
    public function jsonSerialize(): mixed
    {
        return $this->data;
    }
}

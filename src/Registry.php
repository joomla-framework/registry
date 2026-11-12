<?php

/**
 * Part of the Joomla Framework Registry Package
 *
 * @copyright  Copyright (C) 2013 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry;

use Joomla\Utilities\ArrayHelper;

/**
 * Registry class
 *
 * @since  1.0.0
 * @since  2.0.0  `Registry::getInstance()` was removed. Instantiate a new Registry instance instead.
 */
class Registry implements \JsonSerializable, \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Registry Object
     *
     * @var    \stdClass
     * @since  1.0.0
     */
    protected $data;

    /**
     * Flag if the Registry data object has been initialized
     *
     * @var    boolean
     * @since  1.5.2
     */
    protected $initialized = false;

    /**
     * Path separator
     *
     * @var    string
     * @since  1.4.0
     */
    protected $separator = '.';

    /**
     * Constructor
     *
     * @param  mixed   $data       The data to bind to the new Registry object.
     * @param  string  $separator  The path separator, and empty string will flatten the registry.
     *
     * @since   1.0.0
     */
    public function __construct(mixed $data = null, string $separator = '.')
    {
        $this->separator = $separator;

        // Instantiate the internal data object.
        $this->data = new \stdClass();

        // Optionally load supplied data.
        if ($data instanceof self) {
            $this->merge($data);
        } elseif (\is_array($data) || \is_object($data)) {
            $this->bindData($this->data, $data);
        } elseif (!empty($data) && \is_string($data)) {
            $this->loadString($data);
        }
    }

    /**
     * Magic function to clone the registry object.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function __clone(): void
    {
        $this->data = \unserialize(\serialize($this->data));
    }

    /**
     * Magic function to render this object as a string using default args of toString method.
     *
     * @return  string
     *
     * @since   1.0.0
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Count elements of the data object
     *
     * @return  integer  The custom count as an integer.
     *
     * @link    https://www.php.net/manual/en/countable.count.php
     * @since   1.3.0
     */
    public function count(): int
    {
        return \count(\get_object_vars($this->data));
    }

    /**
     * Implementation for the JsonSerializable interface.
     * Allows us to pass Registry objects to json_encode.
     *
     * @return  object
     *
     * @since   1.0.0
     * @note    The interface is only present in PHP 5.4 and up.
     */
    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    /**
     * Sets a default value if not already assigned.
     *
     * @param  string  $key      The name of the parameter.
     * @param  mixed   $default  An optional value for the parameter.
     *
     * @return  mixed  The value set, or the default if the value was not previously set (or null).
     *
     * @since   1.0.0
     */
    public function def(string $key, mixed $default = ''): mixed
    {
        $value = $this->get($key, $default);
        $this->set($key, $value);

        return $value;
    }

    /**
     * Check if a registry path exists.
     *
     * A path exists if it has been set, even if its value is null.
     *
     * @param  string  $path  Registry path (e.g. joomla.content.showauthor)
     *
     * @return  boolean
     *
     * @since   1.0.0
     * @since   __DEPLOY_VERSION__  A path whose value is null exists. Before, exists() followed isset() and returned false.
     */
    public function exists(string $path): bool
    {
        // Return default value if path is empty
        if (empty($path)) {
            return false;
        }

        // Explode the registry path into an array
        if ($this->separator === null || $this->separator === '') {
            $nodes = [$path];
        } else {
            $nodes = \explode($this->separator, $path);
        }

        // Initialize the current node to be the registry root.
        $node  = $this->data;
        $found = false;

        // Traverse the registry to find the correct node for the result.
        foreach ($nodes as $n) {
            if (\is_array($node) && \array_key_exists($n, $node)) {
                $node  = $node[$n];
                $found = true;

                continue;
            }

            if (!\is_object($node) || !\property_exists($node, $n)) {
                return false;
            }

            $node  = $node->$n;
            $found = true;
        }

        return $found;
    }

    /**
     * Get a registry value.
     *
     * @param  string  $path     Registry path (e.g. joomla.content.showauthor)
     * @param  mixed   $default  Optional default value, returned if the internal value is null.
     *
     * @return  mixed  Value of entry or null
     *
     * @since   1.0.0
     * @since   __DEPLOY_VERSION__  A stored empty string is returned. Before, the default value was returned instead.
     */
    public function get(string $path, mixed $default = null): mixed
    {
        // Return default value if path is empty
        if (empty($path)) {
            return $default;
        }

        if ($this->separator === null || $this->separator === '' || !\strpos($path, $this->separator)) {
            return $this->data->$path ?? $default;
        }

        // Explode the registry path into an array
        $nodes = \explode($this->separator, \trim($path));

        // Initialize the current node to be the registry root.
        $node  = $this->data;
        $found = false;

        // Traverse the registry to find the correct node for the result.
        foreach ($nodes as $n) {
            if (\is_array($node) && isset($node[$n])) {
                $node  = $node[$n];
                $found = true;

                continue;
            }

            if (!isset($node->$n)) {
                return $default;
            }

            $node  = $node->$n;
            $found = true;
        }

        if (!$found || $node === null) {
            return $default;
        }

        return $node;
    }

    /**
     * Gets this object represented as an ArrayIterator.
     *
     * This allows the data properties to be accessed via a foreach statement.
     *
     * @return  \ArrayIterator  This object represented as an ArrayIterator.
     *
     * @see     \IteratorAggregate::getIterator()
     * @since   1.3.0
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator((array) $this->data);
    }

    /**
     * Load an associative array of values into the default namespace
     *
     * @param  array    $array      Associative array of value to load
     * @param  boolean  $flattened  Load from a one-dimensional array
     *
     * @return  $this
     *
     * @since   1.0.0
     * @since   2.0.0  The parameter `$array` is now type hinted as `array`. Before 2.0.0, the type was not enforced.
     */
    public function loadArray(array $array, bool $flattened = false): static
    {
        if (!$flattened) {
            $this->bindData($this->data, $array);

            return $this;
        }

        foreach ($array as $k => $v) {
            $this->set($k, $v);
        }

        return $this;
    }

    /**
     * Load the public variables of the object into the default namespace.
     *
     * @param  object  $object  The object holding the publics to load
     *
     * @return  $this
     *
     * @since   1.0.0
     */
    public function loadObject(object $object): static
    {
        $this->bindData($this->data, $object);

        return $this;
    }

    /**
     * Load the contents of a file into the registry
     *
     * @param  string  $file     Path to file to load
     * @param  string  $format   Format of the file [optional: defaults to JSON]
     * @param  array   $options  Options used by the formatter
     *
     * @return  $this
     *
     * @since   1.0.0
     * @since   2.0.0  The parameter `$options` is now type hinted as `array`. Before 2.0.0, the type was not enforced.
     */
    public function loadFile(string $file, string $format = 'JSON', array $options = []): static
    {
        $data = \file_get_contents($file);

        return $this->loadString($data, $format, $options);
    }

    /**
     * Load a string into the registry
     *
     * @param  string  $data     String to load into the registry
     * @param  string  $format   Format of the string
     * @param  array   $options  Options used by the formatter
     *
     * @return  $this
     *
     * @since   1.0.0
     * @since   2.0.0  The parameter `$options` is now type hinted as `array`. Before 2.0.0, the type was not enforced.
     */
    public function loadString(string $data, string $format = 'JSON', array $options = []): static
    {
        // Load a string into the given namespace [or default namespace if not given]
        $obj = Factory::getFormat($format, $options)->stringToObject($data, $options);

        // If the data object has not yet been initialized, direct assign the object
        if (!$this->initialized) {
            $this->data        = $obj;
            $this->initialized = true;

            return $this;
        }

        $this->loadObject($obj);

        return $this;
    }

    /**
     * Merge a Registry object into this one
     *
     * @param  Registry  $source     Source Registry object to merge.
     * @param  boolean   $recursive  True to support recursive merge the children values.
     *
     * @return  $this
     *
     * @since   1.0.0
     * @since   2.0.0  The parameter `$source` is now type hinted as `Registry`. Before 2.0.0, `Registry::merge()` just
     *          returned `false` if `$source` was not a `Registry`.
     */
    public function merge(Registry $source, bool $recursive = false): static
    {
        $this->bindData($this->data, $source->toArray(), $recursive, false);

        return $this;
    }

    /**
     * Method to extract a sub-registry from path
     *
     * @param  string  $path  Registry path (e.g. joomla.content.showauthor)
     *
     * @return  Registry  Registry object (empty if no data is present)
     *
     * @since   1.2.0
     * @since   2.0.0  `Registry:extract()` now always returns a `Registry` object. Before 2.0.0, `null` was returned
     *          if there was no data for the key.
     */
    public function extract(string $path): Registry
    {
        $data = $this->get($path);

        return new Registry($data);
    }

    /**
     * Checks whether an offset exists in the iterator.
     *
     * @param  mixed  $offset  The array offset.
     *
     * @return  boolean  True if the offset exists and its value is not null, false otherwise.
     *
     * @since   1.0.0
     * @since   __DEPLOY_VERSION__  Keeps the isset() semantics, so `isset($registry['key'])` is false for a null value.
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->exists($offset) && $this->get($offset) !== null;
    }

    /**
     * Gets an offset in the iterator.
     *
     * @param  mixed  $offset  The array offset.
     *
     * @return  mixed  The array value if it exists, null otherwise.
     *
     * @since   1.0.0
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Sets an offset in the iterator.
     *
     * @param  mixed  $offset  The array offset.
     * @param  mixed  $value   The array value.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Unsets an offset in the iterator.
     *
     * @param  mixed  $offset  The array offset.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    /**
     * Set a registry value.
     *
     * @param  string  $path       Registry Path (e.g. joomla.content.showauthor)
     * @param  mixed   $value      Value of entry
     *
     * @return  mixed  The value of the that has been set.
     *
     * @since   1.0.0
     */
    public function set(string $path, mixed $value): mixed
    {
        /*
         * Explode the registry path into an array and remove empty
         * nodes that occur as a result of a double separator. ex: joomla..test
         * Finally, re-key the array so they are sequential.
         */
        if ($this->separator === null || $this->separator === '') {
            $nodes = [$path];
        } else {
            $nodes = \array_values(\array_filter(\explode($this->separator, $path), 'strlen'));
        }

        if (!$nodes) {
            return null;
        }

        // Initialize the current node to be the registry root.
        $node = $this->data;

        // Traverse the registry to find the correct node for the result.
        for ($i = 0, $n = \count($nodes) - 1; $i < $n; $i++) {
            if (\is_object($node)) {
                if (!isset($node->{$nodes[$i]})) {
                    $node->{$nodes[$i]} = new \stdClass();
                }

                // Pass the child as pointer in case it is an object
                $node = &$node->{$nodes[$i]};

                continue;
            }

            if (\is_array($node)) {
                if (!isset($node[$nodes[$i]])) {
                    $node[$nodes[$i]] = new \stdClass();
                }

                // Pass the child as pointer in case it is an array
                $node = &$node[$nodes[$i]];
            }
        }

        // Get the old value if exists so we can return it
        switch (true) {
            case (\is_object($node)):
                $result             = $node->{$nodes[$i]} ?? null;
                $node->{$nodes[$i]} = $value;
                break;

            case (\is_array($node)):
                $result           = $node[$nodes[$i]] ?? null;
                $node[$nodes[$i]] = $value;
                break;

            default:
                $result = null;
                break;
        }

        return $result;
    }

    /**
     * Append value to a path in registry
     *
     * @param  string  $path   Parent registry Path (e.g. joomla.content.showauthor)
     * @param  mixed   $value  Value of entry
     *
     * @return  mixed  The value of the that has been set.
     *
     * @since   1.4.0
     */
    public function append(string $path, mixed $value): mixed
    {
        $result = null;

        /*
         * Explode the registry path into an array and remove empty
         * nodes that occur as a result of a double dot. ex: joomla..test
         * Finally, re-key the array so they are sequential.
         */
        if ($this->separator === null || $this->separator === '') {
            $nodes = [$path];
        } else {
            $nodes = \array_values(\array_filter(\explode($this->separator, $path), 'strlen'));
        }

        if ($nodes) {
            // Initialize the current node to be the registry root.
            $node = $this->data;

            // Traverse the registry to find the correct node for the result.
            // TODO Create a new private method from part of code below, as it is almost equal to 'set' method
            for ($i = 0, $n = \count($nodes) - 1; $i <= $n; $i++) {
                if (\is_object($node)) {
                    if (!isset($node->{$nodes[$i]}) && ($i !== $n)) {
                        $node->{$nodes[$i]} = new \stdClass();
                    }

                    // Pass the child as pointer in case it is an array
                    $node = &$node->{$nodes[$i]};
                } elseif (\is_array($node)) {
                    if (($i !== $n) && !isset($node[$nodes[$i]])) {
                        $node[$nodes[$i]] = new \stdClass();
                    }

                    // Pass the child as pointer in case it is an array
                    $node = &$node[$nodes[$i]];
                }
            }

            if (!\is_array($node)) {
                // Convert the node to array to make append possible
                $node = \get_object_vars($node);
            }

            $node[] = $value;
            $result = $value;
        }

        return $result;
    }

    /**
     * Delete a registry value
     *
     * @param  string  $path  Registry Path (e.g. joomla.content.showauthor)
     *
     * @return  mixed  The value of the removed node or null if not set
     *
     * @since   1.6.0
     */
    public function remove(string $path): mixed
    {
        // Cheap optimisation to direct remove the node if there is no separator
        if ($this->separator === null || $this->separator === '' || !\strpos($path, $this->separator)) {
            $result = (isset($this->data->$path) && $this->data->$path !== '')
                ? $this->data->$path
                : null;

            unset($this->data->$path);

            return $result;
        }

        /*
         * Explode the registry path into an array and remove empty
         * nodes that occur as a result of a double separator. ex: joomla..test
         * Finally, re-key the array so they are sequential.
         */
        $nodes = \array_values(\array_filter(\explode($this->separator, $path), 'strlen'));

        if (!$nodes) {
            return null;
        }

        // Initialize the current node to be the registry root.
        $node   = $this->data;
        $parent = null;

        // Traverse the registry to find the correct node for the result.
        for ($i = 0, $n = \count($nodes) - 1; $i < $n; $i++) {
            if (\is_object($node)) {
                if (!isset($node->{$nodes[$i]})) {
                    continue;
                }

                $parent = &$node;
                $node   = $node->{$nodes[$i]};

                continue;
            }

            if (\is_array($node)) {
                if (!isset($node[$nodes[$i]])) {
                    continue;
                }

                $parent = &$node;
                $node   = $node[$nodes[$i]];

                continue;
            }
        }

        // Get the old value if exists so we can return it
        switch (true) {
            case \is_object($node):
                $result = $node->{$nodes[$i]} ?? null;
                unset($parent->{$nodes[$i]});
                break;

            case \is_array($node):
                $result = $node[$nodes[$i]] ?? null;
                unset($parent[$nodes[$i]]);
                break;

            default:
                $result = null;
                break;
        }

        return $result;
    }

    /**
     * Transforms a namespace to an array
     *
     * @return  array  An associative array holding the namespace data
     *
     * @since   1.0.0
     */
    public function toArray(): array
    {
        return $this->asArray($this->data);
    }

    /**
     * Transforms a namespace to an object
     *
     * @return  object   An an object holding the namespace data
     *
     * @since   1.0.0
     */
    public function toObject(): object
    {
        return $this->data;
    }

    /**
     * Get a namespace in a given string format
     *
     * @param  string  $format   Format to return the string in
     * @param  array   $options  Parameters used by the formatter, see formatters for more info
     *
     * @return  string   Namespace in string format
     *
     * @since   1.0.0
     */
    public function toString(string $format = 'JSON', array $options = []): string
    {
        return Factory::getFormat($format, $options)->objectToString($this->data, $options);
    }

    /**
     * Method to recursively bind data to a parent object.
     *
     * @param  object   $parent     The parent object on which to attach the data values.
     * @param  mixed    $data       An array or object of data to bind to the parent object.
     * @param  boolean  $recursive  True to support recursive bindData.
     * @param  boolean  $allowNull  True to allow null values.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function bindData(object $parent, mixed $data, bool $recursive = true, bool $allowNull = true): void
    {
        // The data object is now initialized
        $this->initialized = true;

        // Ensure the input data is an array.
        $data = \is_object($data) ? \get_object_vars($data) : (array) $data;

        foreach ($data as $k => $v) {
            if (!$allowNull && !(($v !== null) && ($v !== ''))) {
                continue;
            }

            if ($recursive && ((\is_array($v) && ArrayHelper::isAssociative($v)) || \is_object($v))) {
                if (!isset($parent->$k)) {
                    $parent->$k = new \stdClass();
                }

                $this->bindData($parent->$k, $v);

                continue;
            }

            $parent->$k = $v;
        }
    }

    /**
     * Method to recursively convert an object of data to an array.
     *
     * @param  object|array  $data  An object of data to return as an array.
     *
     * @return  array  Array representation of the input object.
     *
     * @since   1.0.0
     */
    protected function asArray(object|array $data): array
    {
        $array = [];

        if (\is_object($data)) {
            $data = \get_object_vars($data);
        }

        foreach ($data as $k => $v) {
            if (\is_object($v) || \is_array($v)) {
                $array[$k] = $this->asArray($v);

                continue;
            }

            $array[$k] = $v;
        }

        return $array;
    }

    /**
     * Dump to one dimension array.
     *
     * @param  string  $separator  The key separator.
     *
     * @return  string[]  Dumped array.
     *
     * @since   1.3.0
     */
    public function flatten(?string $separator = null): array
    {
        $array = [];

        if (empty($separator)) {
            $separator = $this->separator;
        }

        $this->toFlatten($separator, $this->data, $array);

        return $array;
    }

    /**
     * Method to recursively convert data to one dimension array.
     *
     * @param  string        $separator  The key separator.
     * @param  array|object  $data       Data source of this scope.
     * @param  array         $array      The result array, it is passed by reference.
     * @param  string        $prefix     Last level key prefix.
     *
     * @return  void
     *
     * @since   1.3.0
     * @since   2.0.0  The parameter `$array` is now type hinted as `array`. Before 2.0.0, the type was not enforced.
     */
    protected function toFlatten(?string $separator = null, mixed $data = null, array &$array = [], string $prefix = ''): void
    {
        $data = (array) $data;

        if (empty($separator)) {
            $separator = $this->separator;
        }

        foreach ($data as $k => $v) {
            $key = $prefix ? $prefix . $separator . $k : $k;

            if (\is_object($v) || \is_array($v)) {
                $this->toFlatten($separator, $v, $array, $key);

                continue;
            }

            $array[$key] = $v;
        }
    }
}

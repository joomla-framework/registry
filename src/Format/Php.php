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
 * PHP class format handler for Registry
 *
 * @since  1.0.0
 */
class Php implements FormatInterface
{
    /**
     * Converts an object into a php class string.
     * - NOTE: Only one depth level is supported.
     *
     * @param  object  $object  Data Source Object
     * @param  array   $params  Parameters used by the formatter
     *
     * @return  string  Config class formatted string
     *
     * @throws  \InvalidArgumentException  if a property name, the class name or the namespace is not a
     *                                     valid PHP identifier and could therefore inject arbitrary code
     *
     * @since   1.0.0
     * @since   2.0.0  The PHP format respects the data type of each value when generating the PHP source code.
     *                 Before 2.0.0, all data were converted to string notation.
     */
    public function objectToString($object, array $params = [])
    {
        // A class must be provided
        $class = $params['class'] ?? 'Registry';

        static::validateIdentifier($class, 'class name');

        // Build the object variables string
        $vars = '';

        foreach (\get_object_vars($object) as $k => $v) {
            static::validateIdentifier($k, 'property name');

            $vars .= "\tpublic \$$k = " . $this->formatValue($v) . ";\n";
        }

        $str = "<?php\n";

        // If supplied, add a namespace to the class object
        if (isset($params['namespace']) && $params['namespace'] !== '') {
            static::validateNamespace($params['namespace']);

            $str .= 'namespace ' . $params['namespace'] . ";\n\n";
        }

        $str .= "class $class {\n";
        $str .= $vars;
        $str .= '}';

        // Use the closing tag if it not set to false in parameters.
        if (!isset($params['closingtag']) || $params['closingtag'] !== false) {
            $str .= "\n?>";
        }

        return $str;
    }

    /**
     * Parse a PHP class formatted string and convert it into an object.
     *
     * @param  string  $data     PHP Class formatted string to convert.
     * @param  array   $options  Options used by the formatter.
     *
     * @return  object   Data object.
     *
     * @since   1.0.0
     */
    public function stringToObject(string $data, array $options = [])
    {
        return new \stdClass();
    }

    /**
     * Ensure a value is a valid PHP identifier before it is written into generated source code.
     *
     * Everything this class emits ends up in a file that will later be executed by PHP. A value that
     * is not a plain identifier can therefore close the current statement and append arbitrary code,
     * so anything unexpected is rejected rather than escaped.
     *
     * @param  mixed   $value  The value to check.
     * @param  string  $label  What the value represents, used in the exception message.
     *
     * @return  void
     *
     * @throws  \InvalidArgumentException
     *
     * @since   __DEPLOY_VERSION__
     */
    protected static function validateIdentifier($value, string $label): void
    {
        if (!\is_string($value) || !\preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $value)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'The %s "%s" is not a valid PHP identifier and cannot be written to a PHP class file.',
                    $label,
                    \is_scalar($value) ? (string) $value : \gettype($value)
                )
            );
        }
    }

    /**
     * Ensure a value is a valid PHP namespace before it is written into generated source code.
     *
     * @param  mixed  $namespace  The namespace to check.
     *
     * @return  void
     *
     * @throws  \InvalidArgumentException
     *
     * @since   __DEPLOY_VERSION__
     */
    protected static function validateNamespace($namespace): void
    {
        if (!\is_string($namespace)) {
            throw new \InvalidArgumentException('The namespace must be a string.');
        }

        foreach (\explode('\\', \ltrim($namespace, '\\')) as $part) {
            static::validateIdentifier($part, 'namespace segment');
        }
    }

    /**
     * Format a value for the string conversion
     *
     * @param  mixed  $value  The value to format
     *
     * @return  mixed  The formatted value
     *
     * @since   2.0.0
     */
    protected function formatValue($value)
    {
        switch (\gettype($value)) {
            case 'string':
                return "'" . \addcslashes($value, '\\\'') . "'";

            case 'array':
            case 'object':
                return $this->getArrayString((array) $value);

            case 'double':
            case 'integer':
                return $value;

            case 'boolean':
                return $value ? 'true' : 'false';

            case 'NULL':
                return 'null';
        }

        return null;
    }

    /**
     * Method to get an array as an exported string.
     *
     * @param  array  $a  The array to get as a string.
     *
     * @return  string
     *
     * @since   1.0.0
     */
    protected function getArrayString($a)
    {
        $s = 'array(';
        $i = 0;

        foreach ($a as $k => $v) {
            $s .= $i ? ', ' : '';
            $s .= "'" . \addcslashes($k, '\\\'') . "' => ";
            $s .= $this->formatValue($v);

            $i++;
        }

        $s .= ')';

        return $s;
    }
}

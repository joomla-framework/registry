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
 * XML format handler for Registry.
 *
 * @since  1.0.0
 */
class Xml implements FormatInterface
{
    /**
     * Converts an object into an XML formatted string.
     * -    If more than two levels of nested groups are necessary, since INI is not
     * useful, XML or another format should be used.
     *
     * @param  object  $object   Data source object.
     * @param  array   $options  Options used by the formatter.
     *
     * @return  string  XML formatted string.
     *
     * @throws  \InvalidArgumentException  if the root or node name is not a valid XML name
     *
     * @since   1.0.0
     */
    public function objectToString($object, array $options = [])
    {
        $rootName = $options['name'] ?? 'registry';
        $nodeName = $options['nodeName'] ?? 'node';

        static::validateName($rootName, 'root');
        static::validateName($nodeName, 'node');

        // Create the root node.
        $root = \simplexml_load_string('<' . $rootName . ' />');

        // Iterate over the object members.
        $this->getXmlChildren($root, $object, $nodeName);

        return $root->asXML();
    }

    /**
     * Parse a XML formatted string and convert it into an object.
     *
     * @param  string  $data     XML formatted string to convert.
     * @param  array   $options  Options used by the formatter.
     *
     * @return  object   Data object.
     *
     * @throws  \RuntimeException  if the data is not well-formed XML
     *
     * @since   1.0.0
     */
    public function stringToObject(string $data, array $options = [])
    {
        $obj = new \stdClass();

        // Parse the XML string, collecting errors instead of emitting warnings and without network access.
        $useInternalErrors = \libxml_use_internal_errors(true);

        $xml    = \simplexml_load_string($data, \SimpleXMLElement::class, LIBXML_NONET);
        $errors = \libxml_get_errors();

        \libxml_clear_errors();
        \libxml_use_internal_errors($useInternalErrors);

        if ($xml === false) {
            throw new \RuntimeException(
                \sprintf('Error parsing XML data: %s', $errors ? \trim($errors[0]->message) : 'unknown error')
            );
        }

        foreach ($xml->children() as $node) {
            $obj->{$node['name']} = $this->getValueFromNode($node);
        }

        return $obj;
    }

    /**
     * Method to get a PHP native value for a SimpleXMLElement object. -- called recursively
     *
     * @param  \SimpleXMLElement  $node  SimpleXMLElement object for which to get the native value.
     *
     * @return  mixed  Native value of the SimpleXMLElement object.
     *
     * @since   1.0.0
     */
    protected function getValueFromNode($node)
    {
        switch ($node['type']) {
            case 'integer':
                $value = (string) $node;

                return (int) $value;

            case 'string':
                return (string) $node;

            case 'boolean':
                $value = (string) $node;

                return (bool) $value;

            case 'double':
                $value = (string) $node;

                return (float) $value;

            case 'array':
                $value = [];

                foreach ($node->children() as $child) {
                    $value[(string) $child['name']] = $this->getValueFromNode($child);
                }

                break;

            default:
                $value = new \stdClass();

                foreach ($node->children() as $child) {
                    $value->{$child['name']} = $this->getValueFromNode($child);
                }

                break;
        }

        return $value;
    }

    /**
     * Method to build a level of the XML string -- called recursively
     *
     * @param  \SimpleXMLElement  $node      SimpleXMLElement object to attach children.
     * @param  object             $var       Object that represents a node of the XML document.
     * @param  string             $nodeName  The name to use for node elements.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function getXmlChildren(\SimpleXMLElement $node, $var, $nodeName)
    {
        // Iterate over the object members.
        foreach ((array) $var as $k => $v) {
            if (\is_scalar($v)) {
                // addChild() treats "&" as the start of an entity reference, so the value must be escaped
                $n = $node->addChild($nodeName, \htmlspecialchars((string) $v, ENT_XML1 | ENT_SUBSTITUTE));
                $n->addAttribute('name', $k);
                $n->addAttribute('type', \gettype($v));
            } else {
                $n = $node->addChild($nodeName);
                $n->addAttribute('name', $k);
                $n->addAttribute('type', \gettype($v));

                $this->getXmlChildren($n, $v, $nodeName);
            }
        }
    }

    /**
     * Ensure a root or node name is a valid XML name before it is used to build the document.
     *
     * @param  mixed   $name   The name to check.
     * @param  string  $label  What the name is used for, used in the exception message.
     *
     * @return  void
     *
     * @throws  \InvalidArgumentException
     *
     * @since   __DEPLOY_VERSION__
     */
    protected static function validateName(mixed $name, string $label): void
    {
        // A letter or underscore, followed by letters, digits, underscores, dots and dashes; colons are not supported
        $pattern = '/^[A-Za-z_\x{80}-\x{10FFFF}][A-Za-z0-9_.\-\x{80}-\x{10FFFF}]*$/uD';

        if (!\is_string($name) || !\preg_match($pattern, $name)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'The %s name "%s" is not a valid XML name.',
                    $label,
                    \is_scalar($name) ? (string) $name : \gettype($name)
                )
            );
        }
    }
}

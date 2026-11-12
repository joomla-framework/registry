<?php

/**
 * Part of the Joomla Framework Registry Package
 *
 * @copyright  Copyright (C) 2013 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Format;

use Joomla\Registry\FormatInterface;
use Symfony\Component\Yaml\Dumper as SymfonyYamlDumper;
use Symfony\Component\Yaml\Parser as SymfonyYamlParser;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;

/**
 * YAML format handler for Registry.
 *
 * @since  1.0.0
 */
class Yaml implements FormatInterface
{
    /**
     * The YAML parser class.
     *
     * @var    SymfonyYamlParser
     * @since  1.0.0
     */
    private $parser;

    /**
     * The YAML dumper class.
     *
     * @var    SymfonyYamlDumper
     * @since  1.0.0
     */
    private $dumper;

    /**
     * Construct to set up the parser and dumper
     *
     * @since   1.0.0
     */
    public function __construct()
    {
        if (!\class_exists(SymfonyYaml::class)) {
            throw new \RuntimeException(
                \sprintf(
                    'The "%s" class could not be found, make sure you have installed the "symfony/yaml" package.',
                    SymfonyYaml::class
                )
            );
        }

        $this->parser = new SymfonyYamlParser();
        $this->dumper = new SymfonyYamlDumper();
    }

    /**
     * Converts an object into a YAML formatted string.
     *
     * @param  object|array  $object   Data source object.
     * @param  array   $options  Options used by the formatter.
     *
     * @return  string  YAML formatted string.
     *
     * @since   1.0.0
     * @since   __DEPLOY_VERSION__  The object is converted to an array directly instead of through JSON.
     */
    public function objectToString($object, array $options = [])
    {
        return $this->dumper->dump($this->toArray($object), 2, 0);
    }

    /**
     * Parse a YAML formatted string and convert it into an object.
     *
     * @param  string  $data     YAML formatted string to convert.
     * @param  array   $options  Options used by the formatter.
     *
     * @return  object  Data object.
     *
     * @since   1.0.0
     * @since   __DEPLOY_VERSION__  The parsed array is converted to an object directly instead of through JSON.
     */
    public function stringToObject(string $data, array $options = [])
    {
        return (object) $this->toObject($this->parser->parse(\trim($data)));
    }

    /**
     * Recursively convert the registry data into arrays for the YAML dumper.
     *
     * Objects are converted the way json_encode() would see them, without the detour through a JSON string that
     * fails for values JSON cannot represent, such as INF or strings that are not valid UTF-8.
     *
     * @param  mixed  $value  The value to convert.
     *
     * @return  mixed
     *
     * @since   __DEPLOY_VERSION__
     */
    private function toArray(mixed $value): mixed
    {
        if ($value instanceof \JsonSerializable) {
            $value = $value->jsonSerialize();
        }

        if ($value instanceof \DateTimeInterface) {
            $value = (array) $value;
        } elseif (\is_object($value)) {
            $value = \get_object_vars($value);
        }

        if (\is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->toArray($item);
            }
        }

        return $value;
    }

    /**
     * Recursively convert the parsed YAML data into objects.
     *
     * Mappings become objects and sequences stay arrays, as json_decode() would return them.
     *
     * @param  mixed  $value  The value to convert.
     *
     * @return  mixed
     *
     * @since   __DEPLOY_VERSION__
     */
    private function toObject(mixed $value): mixed
    {
        if (!\is_array($value)) {
            return $value;
        }

        foreach ($value as $key => $item) {
            $value[$key] = $this->toObject($item);
        }

        return \array_is_list($value) ? $value : (object) $value;
    }
}

<?php

/**
 * @copyright  Copyright (C) 2026 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests;

use Joomla\Registry\Factory;
use Joomla\Registry\Format\Ini;
use Joomla\Registry\Format\Json;
use Joomla\Registry\Format\Php;
use Joomla\Registry\Format\Xml;
use Joomla\Registry\Format\Yaml;
use Joomla\Registry\FormatInterface;
use Joomla\Registry\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Checks that the public API declares native parameter and return types.
 */
class NativeTypesTest extends TestCase
{
    public static function classProvider(): \Generator
    {
        foreach ([Registry::class, Factory::class, FormatInterface::class, Ini::class, Json::class, Php::class, Xml::class, Yaml::class] as $class) {
            yield $class => [$class];
        }
    }

    #[DataProvider('classProvider')]
    public function testMethodsDeclareNativeTypes(string $class)
    {
        $reflection = new \ReflectionClass($class);

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED) as $method) {
            if ($method->getDeclaringClass()->getName() !== $class) {
                continue;
            }

            if ($method->getName() !== '__construct') {
                $this->assertTrue($method->hasReturnType(), $class . '::' . $method->getName() . '() has no return type.');
            }

            foreach ($method->getParameters() as $parameter) {
                $this->assertTrue(
                    $parameter->hasType(),
                    \sprintf('Parameter $%s of %s::%s() has no type.', $parameter->getName(), $class, $method->getName())
                );
            }
        }
    }

    public function testThereAreNoTentativeReturnTypes()
    {
        foreach ((new \ReflectionClass(Registry::class))->getMethods() as $method) {
            $this->assertSame(
                [],
                $method->getAttributes(\ReturnTypeWillChange::class),
                'Registry::' . $method->getName() . '() still uses #[\ReturnTypeWillChange].'
            );
        }
    }
}

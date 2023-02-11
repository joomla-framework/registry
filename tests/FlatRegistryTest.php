<?php

/**
 * @copyright  Copyright (C) 2023 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests;

use Joomla\Registry\FlatRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Registry\Registry.
 */
class FlatRegistryTest extends TestCase
{
    /**
     * @testdox  A Registry instance is instantiated with empty data
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testARegistryInstanceIsInstantiatedWithEmptyData()
    {
        $this->assertCount(0, new FlatRegistry(), 'The Registry data store should be empty.');
    }

    /**
     * @testdox  A Registry instance is instantiated with an array of data
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testARegistryInstanceIsInstantiatedWithAnArrayOfData()
    {
        $this->assertCount(1, new FlatRegistry(['foo' => 'bar']), 'The Registry data store should not be empty.');
    }

    /**
     * @testdox  A Registry instance is instantiated with a string of data
     *
     * @covers   \Joomla\Registry\FlatRegistry
     * @uses     \Joomla\Registry\Factory
     * @uses     \Joomla\Registry\Format\Json
     */
    public function testARegistryInstanceIsInstantiatedWithAStringOfData()
    {
        $this->assertCount(
            1,
            new FlatRegistry(\json_encode(['foo' => 'bar'])),
            'The Registry data store should not be empty.'
        );
    }

    /**
     * @testdox  A Registry instance is instantiated with another Registry
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testARegistryInstanceIsInstantiatedWithAnotherRegistry()
    {
        $this->assertCount(
            1,
            new FlatRegistry(new FlatRegistry(['foo' => 'bar'])),
            'The Registry data store should not be empty.'
        );
    }

    /**
     * @testdox  A Registry instance can be cloned
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testCloningARegistry()
    {
        $a = new FlatRegistry(['a' => '123', 'b' => '456']);
        $b = clone $a;

        $this->assertSame(
            \serialize($a),
            \serialize($b),
            'A cloned Registry should have the same serialized contents as the original.'
        );
        $this->assertNotSame($a, $b, 'A cloned Registry should be a different object from the original.');
    }

    /**
     * @testdox  A Registry instance can be cast as a string
     *
     * @covers   \Joomla\Registry\FlatRegistry
     * @uses     \Joomla\Registry\Factory
     * @uses     \Joomla\Registry\Format\Json
     */
    public function testConvertingARegistryToAString()
    {
        $a = new FlatRegistry(['foo' => 'bar']);

        // Registry::toString() defaults to JSON output
        $this->assertSame(
            (string) $a,
            '{"foo":"bar"}',
            'The magic __toString method should return a JSON formatted Registry.'
        );
    }

    /**
     * @testdox  A Registry instance can be counted
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testCountable()
    {
        $a = new FlatRegistry(
            [
                'foo1'   => 'testtoarray1',
                'foo2'   => 'testtoarray2',
                'config' => [
                    'foo3' => 'testtoarray3',
                ],
            ]
        );

        $this->assertCount(3, $a, 'count() should correctly count the number of data elements.');
    }

    /**
     * @testdox  A Registry instance can be processed through json_encode()
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testJsonSerializingARegistry()
    {
        $this->assertSame(
            \json_encode(new FlatRegistry(['foo' => 'bar'])),
            '{"foo":"bar"}',
            'A Registry\'s data should be encoded to JSON.'
        );
    }

    /**
     * @testdox  A default value is assigned to a key if not already set
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testDefineADefaultValueIfKeyIsNotSet()
    {
        $this->assertSame(
            (new FlatRegistry())->def('foo', 'bar'),
            'bar',
            'Calling def() on an unset key should assign the specified default value.'
        );
    }

    /**
     * @testdox  A default value is not assigned to a key if already set
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testDoNotDefineADefaultValueIfKeyIsSet()
    {
        $this->assertSame(
            (new FlatRegistry(['foo' => 'bar']))->def('foo', 'car'),
            'bar',
            'Calling def() on a key with a value should return the current value.'
        );
    }

    /**
     * @testdox  The Registry validates top level keys exist
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testEnsureTopLevelKeysExist()
    {
        $a = new FlatRegistry(['foo' => 'bar']);

        $this->assertTrue($a->exists('foo'), 'The top level key "foo" should exist.');
        $this->assertFalse($a->exists('goo'), 'The top level key "goo" should not exist.');
    }

    /**
     * @testdox  The Registry does not validate an empty path exists
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testEnsureEmptyPathsDoNotExist()
    {
        $this->assertFalse((new FlatRegistry())->exists(''), 'An empty path should not exist.');
    }

    /**
     * @testdox  The Registry returns the default value when a key is not set.
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testGetReturnsTheDefaultValueWhenAKeyIsNotSet()
    {
        $this->assertNull((new FlatRegistry())->get('foo'), 'The default value should be returned for an unassigned key.');
    }

    /**
     * @testdox  The Registry returns the default value when the path is empty.
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testGetReturnsTheDefaultValueWhenThePathIsEmpty()
    {
        $this->assertNull((new FlatRegistry())->get(''), 'The default value should be returned for an empty path.');
    }

    /**
     * @testdox  The Registry returns the assigned value when a key is set.
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testGetReturnsTheAssignedValueWhenAKeyIsSet()
    {
        $this->assertSame(
            (new FlatRegistry(['foo' => 'bar']))->get('foo'),
            'bar',
            'The value of "foo" should be returned.'
        );
    }

    /**
     * @testdox  The Registry correctly handles assignments for integer zero.
     *
     * @covers   \Joomla\Registry\FlatRegistry
     * @ticket   https://github.com/joomla/jissues/issues/629
     */
    public function testTheRegistryCorrectlyHandlesAssignmentsForIntegerZero()
    {
        $a = new FlatRegistry();
        $a->set('foo', 0);
        $a->set('goo.bar', 0);

        $this->assertSame(0, $a->get('foo'), 'The Registry correctly handles when a top level key has a value of 0');
        $this->assertSame(0, $a->get('goo.bar'), 'The Registry correctly handles when a key has a value of 0');
    }

    /**
     * @testdox  The Registry correctly handles assignments for class instances.
     *
     * @covers   \Joomla\Registry\FlatRegistry
     * @ticket   https://github.com/joomla-framework/registry/issues/8
     */
    public function testTheRegistryCorrectlyHandlesAssignmentsForClassInstances()
    {
        $class = new class {
        };

        $a = new FlatRegistry();
        $a->set('class', $class);
        $a->set('nested.class', $class);

        $this->assertSame(
            $class,
            $a->get('class'),
            'The Registry correctly handles when a top level key is an instance of a class'
        );
        $this->assertSame(
            $class,
            $a->get('nested.class'),
            'The Registry correctly handles when a key is an instance of a class'
        );
    }

    /**
     * @testdox  The Registry can be iterated
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testTheRegistryCanBeIterated()
    {
        $this->assertInstanceOf('ArrayIterator', (new FlatRegistry())->getIterator());
    }

    /**
     * @testdox  The Registry can load an array
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testAnArrayCanBeLoaded()
    {
        $registry = new FlatRegistry();

        $this->assertSame(
            $registry->loadArray(['foo' => 'bar']),
            $registry,
            'The loadArray() method should return $this'
        );
        $this->assertSame('bar', $registry->get('foo'), 'The array\'s data should be correctly loaded.');
    }

    /**
     * @testdox  The Registry can load a flattened array
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testAFlattenedArrayCanBeLoaded()
    {
        $array    = [
            'foo.bar'  => 1,
            'foo.test' => 2,
            'bar'      => 3,
        ];
        $registry = new FlatRegistry();

        $this->assertSame($registry->loadArray($array, true), $registry, 'The loadArray() method should return $this');
        $this->assertSame(1, $registry->get('foo.bar'), 'The flattened array\'s data should be correctly loaded.');
    }

    /**
     * @testdox  The Registry can load an object
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testAnObjectCanBeLoaded()
    {
        $object      = new \stdClass();
        $object->foo = 'testloadobject';
        $registry    = new FlatRegistry();

        $this->assertSame($registry, $registry->loadObject($object), 'The loadObject() method should return $this');
        $this->assertSame('testloadobject', $registry->get('foo'), 'The object\'s data should be correctly loaded.');
    }

    /**
     * @testdox  The Registry can load a file
     *
     * @covers   \Joomla\Registry\FlatRegistry
     * @uses     \Joomla\Registry\Factory
     * @uses     \Joomla\Registry\Format\Json
     */
    public function testAFileCanBeLoaded()
    {
        $registry = new FlatRegistry();

        $this->assertSame(
            $registry->loadFile(__DIR__ . '/Stubs/jregistry.json'),
            $registry,
            'The loadFile() method should return $this'
        );
        $this->assertSame('bar', $registry->get('foo'), 'The file\'s data should be correctly loaded.');
    }

    /**
     * @testdox  The Registry can load a string
     *
     * @covers   \Joomla\Registry\FlatRegistry
     * @uses     \Joomla\Registry\Factory
     * @uses     \Joomla\Registry\Format\Ini
     */
    public function testAStringCanBeLoaded()
    {
        $registry = new FlatRegistry();

        $this->assertSame(
            $registry->loadString('foo="testloadini1"', 'INI'),
            $registry,
            'The loadString() method should return $this'
        );
        $this->assertSame('testloadini1', $registry->get('foo'), 'The string\'s data should be correctly loaded.');
    }

    /**
     * @testdox  Two Registry instances can be merged
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testTwoRegistryInstancesCanBeMerged()
    {
        $array1 = [
            'foo' => 'bar',
            'hoo' => 'hum',
            'dum' => [
                'dee' => 'dum',
            ],
        ];

        $array2 = [
            'foo' => 'soap',
            'dum' => 'huh',
        ];

        $registry1 = new FlatRegistry($array1);
        $registry2 = new FlatRegistry($array2);

        $this->assertSame($registry1->merge($registry2), $registry1, 'The merge() method should return $this');
        $this->assertSame(
            'soap',
            $registry1->get('foo'),
            'The second Registry instance\'s data should be correctly merged into the first.'
        );
    }

    /**
     * @testdox  A subset of data can be extracted to a new FlatRegistry
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testASubsetOfDataCanBeExtractedToANewRegistry()
    {
        $a = new FlatRegistry(
            [
                'foo'    => 'bar',
                'subset' => [
                    'data1' => 'test1',
                    'data2' => 'test2',
                    'data3' => [1, 2, 3],
                ],
            ]
        );

        $b = $a->extract('subset');

        $this->assertInstanceOf(FlatRegistry::class, $b, 'The extracted data should be a Registry instance.');
        $this->assertNotSame($a, $b, 'The extracted Registry should be a new FlatRegistry instance.');
        $this->assertNull(
            $b->get('foo'),
            'The extracted Registry should not contain data that is not part of a subset.'
        );
    }

    /**
     * @testdox  A Registry can be extracted from null data
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testARegistryCanBeExtractedFromNullData()
    {
        $a = new FlatRegistry();
        $b = $a->extract('foo');

        $this->assertInstanceOf(FlatRegistry::class, $b, 'A Registry can be extracted from null data.');
        $this->assertNotSame($a, $b, 'Extracting a Registry should always create a new instance.');
    }

    /**
     * @testdox  The array offset is correctly checked
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testCheckOffsetAsAnArray()
    {
        $instance = new FlatRegistry();

        $this->assertEmpty($instance['foo.bar'], 'Checks an offset is empty.');

        $instance->set('foo.bar', 'value');

        $this->assertTrue(isset($instance['foo.bar']), 'Checks a known offset by isset.');
        $this->assertFalse(isset($instance['goo.car']), 'Checks an uknown offset.');
    }

    /**
     * @testdox  The array offset is correctly retrieved
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testGetOffsetAsAnArray()
    {
        $instance = new FlatRegistry(['foo.bar' => 'value']);

        $this->assertSame('value', $instance['foo.bar'], 'Checks a known offset.');
        $this->assertNull($instance['goo.car'], 'Checks a unknown offset.');
    }

    /**
     * @testdox  The array offset is correctly set
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testSetOffsetAsAnArray()
    {
        $instance = new FlatRegistry();

        $instance['foo.bar'] = 'value';
        $this->assertSame('value', $instance->get('foo.bar'));
    }

    /**
     * @testdox  The array offset is correctly removed
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testRemoveOffsetAsAnArray()
    {
        $instance = new FlatRegistry();
        $instance->set('foo.bar', 'value');

        unset($instance['foo.bar']);
        $this->assertFalse(isset($instance['foo.bar']));
    }

    /**
     * @testdox  A value is stored to the Registry
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testAValueIsStoredToTheRegistry()
    {
        $a = new FlatRegistry();

        $this->assertSame(
            null,
            $a->set('foo', 'testsetvalue1'),
            'null should be returned when assigning a key for the first time.'
        );
        $this->assertSame(
            'testsetvalue1',
            $a->set('foo', 'testsetvalue2'),
            'The previous value should be returned when assigning to a key.'
        );
    }

    /**
     * @testdox  A key is appended to a path
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testAKeyIsAppendedToAPath()
    {
        $a = new FlatRegistry(['foo' => ['var1']]);
        $a->append('foo', 'var2');

        $this->assertSame(['var1', 'var2'], $a->get('foo'), 'A key is appended to a path.');

        $a = new FlatRegistry(['foo' => 'var1']);
        $a->append('foo', 'var2');

        $this->assertSame('var2', $a->get('foo'), 'A key is overriding a non array value while append to a path.');
    }

    /**
     * @testdox  A key is removed from the Registry
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testAKeyIsRemovedFromTheRegistry()
    {
        $a = new FlatRegistry(['foo' => 'bar']);

        $this->assertSame(
            'bar',
            $a->remove('foo'),
            'When removing a key from the Registry its old value should be returned.'
        );
        $this->assertFalse($a->exists('foo'));
    }

    /**
     * @testdox  The Registry is unchanged when deleting a non-existing value
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testTheRegistryIsUnchangedWhenDeletingANonExistingValue()
    {
        $a = new FlatRegistry(['foo' => 'bar']);

        $this->assertNull($a->remove('goo'));
        $this->assertNull($a->remove('nested.goo'));

        $this->assertEquals($a->toArray(), ['foo' => 'bar']);
    }

    /**
     * @testdox  The Registry can be converted to an array
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testTheRegistryCanBeConvertedToAnArray()
    {
        $this->assertSame(
            [
                'foo1'   => 'testtoarray1',
                'foo2'   => 'testtoarray2',
                'config' => ['foo3' => 'testtoarray3'],
            ],
            (new FlatRegistry(['foo1' => 'testtoarray1', 'foo2' => 'testtoarray2', 'config' => ['foo3' => 'testtoarray3']]))->toArray(),
            'The Registry should be converted to an array.'
        );
    }

    /**
     * @testdox  The Registry can be converted to an object
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testTheRegistryCanBeConvertedToAnObject()
    {
        $expected               = new \stdClass();
        $expected->foo1         = 'testtoobject1';
        $expected->foo2         = 'testtoobject2';
        $expected->config       = new \stdClass();
        $expected->config->foo3 = 'testtoobject3';

        $this->assertEquals(
            $expected,
            (new FlatRegistry(
                ['foo1' => 'testtoobject1', 'foo2' => 'testtoobject2', 'config' => ['foo3' => 'testtoobject3']]
            ))->toObject(),
            'The Registry should be converted to an object.'
        );
    }

    /**
     * @testdox  The Registry can be converted to a string
     *
     * @covers   \Joomla\Registry\FlatRegistry
     * @uses     \Joomla\Registry\Factory
     * @uses     \Joomla\Registry\Format\Json
     */
    public function testTheRegistryCanBeConvertedToAString()
    {
        $a = new FlatRegistry(
            ['foo1' => 'testtostring1', 'foo2' => 'testtostring2', 'config' => ['foo3' => 'testtostring3']]
        );
        $a->set('foo1', 'testtostring1');
        $a->set('foo2', 'testtostring2');
        $a->set('config.foo3', 'testtostring3');

        $this->assertSame(
            '{"foo1":"testtostring1","foo2":"testtostring2","config":{"foo3":"testtostring3"},"config.foo3":"testtostring3"}',
            trim($a->toString('JSON')),
            'The Registry is converted to a JSON string.'
        );
    }

    /**
     * @testdox  The Registry can be flattened to an array
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testTheRegistryCanBeFlattenedToAnArray()
    {
        $a = new FlatRegistry(['flower' => 'sunflower']);

        $flattened = $a->flatten();

        $this->assertEquals($flattened['flower'], 'sunflower', 'The Registry is flattened to an array.');
    }
}

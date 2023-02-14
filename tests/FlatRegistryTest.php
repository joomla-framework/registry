<?php

/**
 * @copyright  Copyright (C) 2023 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests;

use Joomla\Registry\FlatRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Registry\FlatRegistry.
 */
class FlatRegistryTest extends TestCase
{
    /**
     * @testdox  A FlatRegistry instance is instantiated with empty data
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testInitEmpty()
    {
        $this->assertCount(0, new FlatRegistry(), 'The Registry data store should be empty.');
    }

    /**
     * @testdox  A FlatRegistry instance doing exists() check correctly
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testExistsMethod()
    {
        $a = new FlatRegistry();

        $a->set('foo', 'bar');
        $a->set('bar', null);

        $this->assertSame(true, $a->exists('foo'));
        $this->assertSame(true, $a->exists('bar'));
        $this->assertSame(false, $a->exists('foobar'));
    }

    /**
     * @testdox  A FlatRegistry instance doing set() get() correctly
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testSetGetMethod()
    {
        $a = new FlatRegistry();

        $a->set('foo', 'bar');
        $a->set('bar', 'foo');

        $this->assertSame('bar', $a->get('foo'));
        $this->assertSame('foo', $a->get('bar'));
        $this->assertSame('foo', $a->set('bar', 'bar'), 'The set() method should return previous value.');
        $this->assertCount(2, $a, 'The Registry data store should not be empty.');
    }

    /**
     * @testdox  A FlatRegistry instance doing get() with default value correctly
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testGetDefaultMethod()
    {
        $a = new FlatRegistry();

        $a->set('a', '');
        $a->set('b', null);
        $a->set('c', false);
        $a->set('d', 0);
        $a->set('e', 'Foo bar');

        $this->assertSame('foo', $a->get('a', 'foo'), 'The get() method should return default value.');
        $this->assertSame('foo', $a->get('b', 'foo'), 'The get() method should return default value.');
        $this->assertSame(false, $a->get('c', 'foo'), 'The get() method should return non default value.');
        $this->assertSame(0, $a->get('d', 'foo'), 'The get() method should return non default value.');
        $this->assertSame('Foo bar', $a->get('e', 'foo'), 'The get() method should return non default value.');
    }

    /**
     * @testdox  A FlatRegistry instance doing remove() correctly
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testRemoveMethod()
    {
        $a = new FlatRegistry();

        $a->set('foo', 'bar');
        $a->set('bar', 'foo');

        $this->assertSame('bar', $a->remove('foo'), 'The remove() method should return previous value.');
        $this->assertCount(1, $a, 'The Registry data store should be reduced after removing a value.');
    }

    /**
     * @testdox  A FlatRegistry instance loadData() implementation
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testLoadDataMethod()
    {
        $a = new FlatRegistry();
        $b = new FlatRegistry();

        $a->loadData(['a' => 1]);
        $a->loadData((object) ['b' => 2]);

        $b->loadData($a->toArray());

        $this->assertSame(1, $a->get('a'));
        $this->assertSame(2, $a->get('b'));

        $this->assertSame(1, $b->get('a'));
        $this->assertSame(2, $b->get('b'));
    }

    /**
     * @testdox  A FlatRegistry instance JsonSerializable interface implementation
     *
     * @covers   \Joomla\Registry\FlatRegistry
     */
    public function testJsonInterface()
    {
        $a = new FlatRegistry();

        $a->set('a', 1);
        $a->set('b', '2');

        $b = new FlatRegistry();

        $b->set(0, 0);
        $b->set(1, '1');

        $this->assertSame('{"a":1,"b":"2"}', json_encode($a));
        $this->assertSame('[0,"1"]', json_encode($b));
    }
}

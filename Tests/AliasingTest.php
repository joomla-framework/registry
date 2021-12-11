<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\DI\Tests;

use Joomla\DI\Container;
use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/Stubs/stubs.php';

/**
 * Tests for Container class.
 */
class AliasingTest extends TestCase
{
	/**
	 * @testdox  Both the original key and the alias return the same resource
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testResolveAliasSameAsKey()
	{
		$container = new Container;
		$container->set(
			'foo',
			static function ()
			{
				return new \stdClass;
			},
			true,
			true
		);
		$container->alias('bar', 'foo');

		$this->assertSame(
			$container->get('foo'),
			$container->get('bar'),
			'When retrieving an alias of a class, both the original and the alias should return the same object instance.'
		);
	}

	/**
	 * @testdox  has() also resolves the alias if set.
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testExistsResolvesAlias()
	{
		$container = new Container;
		$container->set(
			'foo',
			static function ()
			{
				return new \stdClass;
			},
			true,
			true
		);
		$container->alias('bar', 'foo');

		$this->assertTrue($container->has('foo'), "Original 'foo' was not resolved");
		$this->assertTrue($container->has('bar'), "Alias 'bar' was not resolved");
	}
}

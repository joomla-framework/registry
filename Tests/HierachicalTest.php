<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\DI\Tests;

use Joomla\DI\Container;
use PHPUnit\Framework\TestCase;

include_once __DIR__.'/Stubs/stubs.php';
include_once __DIR__.'/Stubs/ArbitraryInteropContainer.php';

/**
 * Tests for Container class.
 */
class HierachicalTest extends TestCase
{
	/**
	 * @testdox  Child container has access to parent's resources
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testCreateChild()
	{
		$container = new Container;
		$container->set(
			StubInterface::class,
			static function ()
			{
				return new Stub1;
			}
		);

		$child = $container->createChild();
		$this->assertInstanceOf(Stub1::class, $child->get(StubInterface::class));
	}

	/**
	 * @testdox  Child container resolves parent's alias to parent's resource
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testChildResolveAlias()
	{
		$container = new Container;
		$container->set(
			StubInterface::class,
			static function ()
			{
				return new Stub1;
			}
		);
		$container->alias('stub', StubInterface::class);

		$child = $container->createChild();
		$this->assertInstanceOf(Stub1::class, $child->get('stub'));
	}

	/**
	 * @testdox  Container can decorate an arbitrary PSR-11 compatible container
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testDecorateArbitraryPsr11Container()
	{
		$container = new Container(new \ArbitraryInteropContainer());

		$this->assertTrue($container->has('aic_foo'), "Container does not know 'aic_foo'");
		$this->assertEquals('aic_foo_content', $container->get('aic_foo'), "Container does not return the correct value for 'aic_foo'");
	}

	/**
	 * @testdox  Container can manage an alias for a resource from an arbitrary PSR-11 compatible container
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testDecorateArbitraryPsr11ContainerAlias()
	{
		$container = new Container(new \ArbitraryInteropContainer());
		$container->alias('foo', 'aic_foo');

		$this->assertTrue($container->has('foo'), "Container does not know alias 'foo'");
		$this->assertEquals('aic_foo_content', $container->get('foo'), "Container does not return the correct value for alias 'foo'");
	}

	/**
	 * @testdox  Resources from an arbitrary PSR-11 compatible container are 'shared' and 'protected'
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testDecorateArbitraryPsr11ContainerModes()
	{
		$container = new Container(new \ArbitraryInteropContainer());

		$this->assertTrue($container->isShared('aic_foo'), "'aic_foo' is expected to be shared");
		$this->assertTrue($container->isProtected('aic_foo'), "'aic_foo' is expected to be protected");
	}
}

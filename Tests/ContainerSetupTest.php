<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\DI\Tests;

use Joomla\DI\Container;
use Joomla\DI\Exception\KeyNotFoundException;
use PHPUnit\Framework\TestCase;

include_once __DIR__.'/Stubs/stubs.php';

/**
 * Tests for Container class.
 */
class ContainerSetupTest extends TestCase
{
	/**
	 * Callable object method.
	 */
	public function callMe()
	{
		return 'called';
	}

	/**
	 * @testdox  Resources can be set up with Callables
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testSetCallable()
	{
		$container = new Container;
		$container->set(
			'foo',
			[$this, 'callMe']
		);

		$this->assertSame('called', $container->get('foo'));
	}

	/**
	 * @testdox  Resources can be set up with Closures
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testSetClosure()
	{
		$container = new Container;
		$container->set(
			'foo',
			static function ()
			{
				return 'called';
			}
		);

		$this->assertSame('called', $container->get('foo'));
	}

	/**
	 * @testdox  Resources can be scalar values
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testSetNotCallable()
	{
		$container = new Container;
		$container->set('foo', 'bar');

		$this->assertSame('bar', $container->get('foo'));
	}

	/**
	 * @testdox  Setting an existing protected resource throws an OutOfBoundsException
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testSetAlreadySetProtected()
	{
		$this->expectException(\OutOfBoundsException::class);

		$container = new Container;
		$container->set(
			'foo',
			static function ()
			{
			},
			false,
			true
		);
		$container->set(
			'foo',
			static function ()
			{
			},
			false,
			true
		);
	}

	/**
	 * @testdox  Setting an existing non-protected resource replaces the resource
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testSetAlreadySetNotProtected()
	{
		$container = new Container;
		$container->set(
			'foo',
			static function ()
			{
				return 'original';
			}
		);

		$container->set(
			'foo',
			static function ()
			{
				return 'changed';
			}
		);
		$this->assertSame('changed', $container->get('foo'));
	}

	/**
	 * @testdox  Default mode is 'not shared' and 'not protected'
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testSetDefault()
	{
		$container = new Container;
		$container->set(
			'foo',
			static function ()
			{
				return new \stdClass;
			}
		);

		$this->assertFalse($container->isShared('foo'));
		$this->assertFalse($container->isProtected('foo'));
	}

	public function dataForSetFlags(): \Generator
	{
		yield 'shared, protected' => [
			'shared'    => true,
			'protected' => true,
		];
		yield 'shared, not protected' => [
			'shared'    => true,
			'protected' => false,
		];
		yield 'not shared, protected' => [
			'shared'    => false,
			'protected' => true,
		];
		yield 'not shared, not protected' => [
			'shared'    => false,
			'protected' => false,
		];
	}

	/**
	 * @testdox  'shared' and 'protected' mode can be set independently
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 *
	 * @dataProvider dataForSetFlags
	 */
	public function testSetSharedProtected(bool $shared, bool $protected)
	{
		$container = new Container;
		$container->set(
			'foo',
			static function ()
			{
				return new \stdClass;
			},
			$shared,
			$protected
		);

		$this->assertSame($shared, $container->isShared('foo'));
		$this->assertSame($protected, $container->isProtected('foo'));
	}

	/**
	 * @testdox  The convenience method protect() sets resources as protected, but not as shared by default
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testProtect()
	{
		$container = new Container;
		$container->protect(
			'foo',
			static function ()
			{
				return new \stdClass;
			}
		);

		$this->assertFalse($container->isShared('foo'));
		$this->assertTrue($container->isProtected('foo'));
	}

	/**
	 * @testdox  The convenience method protect() sets resources as shared when passed true as third arg
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testProtectShared()
	{
		$container = new Container;
		$container->protect(
			'foo',
			static function ()
			{
				return new \stdClass;
			},
			true
		);

		$this->assertTrue($container->isShared('foo'));
		$this->assertTrue($container->isProtected('foo'));
	}

	/**
	 * @testdox  The convenience method share() sets resources as shared, but not as protected by default
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testShare()
	{
		$container = new Container;
		$container->share(
			'foo',
			static function ()
			{
				return new \stdClass;
			}
		);

		$this->assertTrue($container->isShared('foo'));
		$this->assertFalse($container->isProtected('foo'));
	}

	/**
	 * @testdox  The convenience method share() sets resources as protected when passed true as third arg
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testShareProtected()
	{
		$container = new Container;
		$container->share(
			'foo',
			static function ()
			{
				return new \stdClass;
			},
			true
		);

		$this->assertTrue($container->isShared('foo'));
		$this->assertTrue($container->isProtected('foo'));
	}

	/**
	 * @testdox  The callback gets the container instance as a parameter
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testGetPassesContainerInstanceShared()
	{
		$container = new Container;
		$container->set(
			'foo',
			static function (Container $c)
			{
				return $c;
			}
		);

		$this->assertSame($container, $container->get('foo'));
	}

	/**
	 * @testdox  The setting an object and then setting it again as null should remove the object
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testSettingNullUnsetsAResource()
	{
		$this->expectException(KeyNotFoundException::class);

		$container = new Container;
		$container->set(
			'foo',
			static function ()
			{
				return 'original';
			}
		);

		$container->set(
			'foo',
			null
		);

		$container->get('foo');
	}
}

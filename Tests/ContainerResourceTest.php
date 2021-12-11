<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\DI\Tests;

use Joomla\DI\Container;
use Joomla\DI\ContainerResource;
use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/Stubs/stubs.php';

/**
 * Tests for ContainerResource class.
 */
class ContainerResourceTest extends TestCase
{
	public function dataInstantiation(): \Generator
	{
		yield 'shared, protected' => [
			'mode'      => ContainerResource::SHARE | ContainerResource::PROTECT,
			'shared'    => true,
			'protected' => true,
		];

		yield 'shared, not protected (explicit)' => [
			'mode'      => ContainerResource::SHARE | ContainerResource::NO_PROTECT,
			'shared'    => true,
			'protected' => false,
		];

		yield 'not shared, protected (explicit)' => [
			'mode'      => ContainerResource::NO_SHARE | ContainerResource::PROTECT,
			'shared'    => false,
			'protected' => true,
		];

		yield 'not shared, not protected (explicit)' => [
			'mode'      => ContainerResource::NO_SHARE | ContainerResource::NO_PROTECT,
			'shared'    => false,
			'protected' => false,
		];

		yield 'shared, not protected (implicit)' => [
			'mode'      => ContainerResource::SHARE,
			'shared'    => true,
			'protected' => false,
		];

		yield 'not shared, protected (implicit)' => [
			'mode'      => ContainerResource::PROTECT,
			'shared'    => false,
			'protected' => true,
		];

		yield 'not shared, not protected (implicit)' => [
			'mode'      => null,
			'shared'    => false,
			'protected' => false,
		];
	}

	/**
	 * @testdox  The resource supports 'shared' and 'protected' modes, defaulting to 'not shared' and 'not protected'
	 *
	 * @covers   Joomla\DI\ContainerResource
	 * @uses     Joomla\DI\Container
	 *
	 * @dataProvider dataInstantiation
	 */
	public function testInstantiation(?int $mode, bool $shared, bool $protected)
	{
		$container = new Container;

		if ($mode === null)
		{
			$descriptor = new ContainerResource($container, 'dummy');
		}
		else
		{
			$descriptor = new ContainerResource($container, 'dummy', $mode);
		}

		$this->assertSame($shared, $descriptor->isShared());
		$this->assertSame($protected, $descriptor->isProtected());
	}

	/**
	 * @testdox  If a factory is provided, the instance is created on retrieval
	 *
	 * @covers   Joomla\DI\ContainerResource
	 * @uses     Joomla\DI\Container
	 */
	public function testGetInstanceWithFactory()
	{
		$container = new Container;

		$resource = new ContainerResource(
			$container,
			static function ()
			{
				return new Stub6;
			}
		);

		$this->assertInstanceOf(Stub6::class, $resource->getInstance());
	}

	/**
	 * @testdox  If a factory is provided in non-shared mode, the instance is not cached
	 *
	 * @covers   Joomla\DI\ContainerResource
	 * @uses     Joomla\DI\Container
	 */
	public function testGetInstanceWithFactoryInNonSharedMode()
	{
		$container = new Container;
		$resource  = new ContainerResource(
			$container,
			static function ()
			{
				return new Stub6;
			},
			ContainerResource::NO_SHARE
		);

		$this->assertNotSame($resource->getInstance(), $resource->getInstance());
	}

	/**
	 * @testdox  If a factory is provided in shared mode, the instance is cached
	 *
	 * @covers   Joomla\DI\ContainerResource
	 * @uses     Joomla\DI\Container
	 */
	public function testGetInstanceWithFactoryInSharedMode()
	{
		$container = new Container;
		$resource  = new ContainerResource(
			$container,
			static function ()
			{
				return new Stub6;
			},
			ContainerResource::SHARE
		);

		$this->assertSame($resource->getInstance(), $resource->getInstance());
	}

	/**
	 * @testdox  If an instance is provided directly in shared mode, that instance is returned
	 *
	 * @covers   Joomla\DI\ContainerResource
	 * @uses     Joomla\DI\Container
	 */
	public function testGetInstanceWithInstanceInSharedMode()
	{
		$stub = new Stub6;
		$container = new Container;
		$resource  = new ContainerResource(
			$container,
			$stub,
			ContainerResource::SHARE
		);

		$this->assertSame($stub, $resource->getInstance());
	}

	/**
	 * @testdox  If an instance is provided directly in non-shared mode, a copy (clone) of that instance is returned
	 *
	 * @covers   Joomla\DI\ContainerResource
	 * @uses     Joomla\DI\Container
	 */
	public function testGetInstanceWithInstanceInNonSharedMode()
	{
		$stub      = new Stub6;
		$container = new Container;
		$resource  = new ContainerResource(
			$container,
			$stub,
			ContainerResource::NO_SHARE
		);

		$this->assertNotSame($stub, $resource->getInstance());
	}

	/**
	 * @testdox  After a reset, a new instance is returned even for shared resources with factories
	 *
	 * @covers   Joomla\DI\ContainerResource
	 * @uses     Joomla\DI\Container
	 */
	public function testResetWithFactory()
	{
		$container = new Container;
		$resource  = new ContainerResource(
			$container,
			static function ()
			{
				return new Stub6;
			},
			ContainerResource::SHARE
		);

		$one = $resource->getInstance();

		$resource->reset();

		$two = $resource->getInstance();

		$this->assertNotSame($one, $two);
	}

	/**
	 * @testdox  After a reset, a new instance is returned even for shared resources with instances
	 *
	 * @covers   Joomla\DI\ContainerResource
	 * @uses     Joomla\DI\Container
	 */
	public function testResetWithInstance()
	{
		$stub      = new Stub6;
		$container = new Container;
		$resource  = new ContainerResource(
			$container,
			$stub,
			ContainerResource::SHARE
		);

		$one = $resource->getInstance();

		$resource->reset();

		$two = $resource->getInstance();

		$this->assertNotSame($one, $two);
	}
}

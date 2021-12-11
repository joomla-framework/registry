<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\DI\Tests;

use Joomla\DI\Container;
use Joomla\DI\Exception\KeyNotFoundException;
use Joomla\DI\Exception\ProtectedKeyException;
use PHPUnit\Framework\TestCase;

include_once __DIR__.'/Stubs/stubs.php';

/**
 * Tests for Container class.
 */
class ResourceDecoration extends TestCase
{
	/**
	 * Value used within resource methods
	 *
	 * @var  integer
	 */
	private $value = 42;

	/**
	 * @testdox  An extended resource replaces the original resource with a Closure
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testExtendClosure()
	{
		$container = new Container;
		$container->share(
			'foo',
			static function ()
			{
				return new \stdClass;
			}
		);

		$container->extend(
			'foo',
			function ($shared)
			{
				$shared->value = $this->value;

				return $shared;
			}
		);

		$one = $container->get('foo');
		$this->assertEquals($this->value, $one->value);

		$two = $container->get('foo');
		$this->assertEquals($this->value, $two->value);

		$this->assertSame($one, $two);
	}

	/**
	 * @testdox  An extended resource replaces the original resource with a callback function
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testExtendCallback()
	{
		$container = new Container;
		$container->share(
			'foo',
			[$this, 'baseCallable']
		);

		$container->extend(
			'foo',
			[$this, 'decoratingCallable']
		);

		$one = $container->get('foo');
		$this->assertEquals($this->value, $one->value);

		$two = $container->get('foo');
		$this->assertEquals($this->value, $two->value);

		$this->assertSame($one, $two);
	}

	/**
	 * @testdox  Scalar resources can be extended
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testExtendScalar()
	{
		$container = new Container;

		$container->set('foo', 'bar');

		$this->assertEquals('bar', $container->get('foo'));

		$container->extend(
			'foo',
			static function ($originalResult, Container $c)
			{
				return $originalResult . 'baz';
			}
		);

		$this->assertEquals('barbaz', $container->get('foo'));
	}

	/**
	 * @testdox  Attempting to extend an undefined resource throws a KeyNotFoundException
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testExtendValidatesKeyIsPresent()
	{
		$this->expectException(KeyNotFoundException::class);

		(new Container)->extend('foo', static function () {});
	}

	/**
	 * @testdox  A protected resource can not be extended
	 *
	 * @covers   Joomla\DI\Container
	 * @uses     Joomla\DI\ContainerResource
	 */
	public function testExtendProtected()
	{
		$this->expectException(ProtectedKeyException::class);

		$container = new Container;
		$container->protect(
			'foo',
			static function ()
			{
				return new \stdClass;
			}
		);

		$container->extend(
			'foo',
			static function ($shared)
			{
				$shared->value = $this->value;

				return $shared;
			}
		);
	}

	/**
	 * A base method defining a resource in a container
	 *
	 * @return  \stdClass
	 */
	public function baseCallable()
	{
		return new \stdClass;
	}

	/**
	 * Method defining the decorating instruction for a container's resource
	 *
	 * @param   \stdClass  $shared  The return from the resource that was decorated
	 *
	 * @return  \stdClass
	 */
	public function decoratingCallable($shared)
	{
		$shared->value = $this->value;

		return $shared;
	}
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Router\Tests;

use Joomla\Router\Route;
use PHPUnit\Framework\TestCase;
use SuperClosure\SerializableClosure;

/**
 * Tests for the Joomla\Router\Route class.
 */
class RouteTest extends TestCase
{
	/**
	 * @testdox  Ensure the Route is instantiated correctly.
	 *
	 * @covers   Joomla\Router\Route
	 */
	public function testInstantiationWithSingleSegmentRoute()
	{
		$route = new Route(['GET'], 'login', 'login', [], ['_format' => 'html']);

		$this->assertSame(['GET'], $route->getMethods());
		$this->assertSame('login', $route->getPattern());
		$this->assertSame('login', $route->getController());
		$this->assertSame([], $route->getRules());
		$this->assertSame(['_format' => 'html'], $route->getDefaults());
		$this->assertSame(\chr(1) . '^login$' . \chr(1), $route->getRegex());
		$this->assertSame([], $route->getRouteVariables());
	}

	/**
	 * @testdox  Ensure the Route is instantiated correctly.
	 *
	 * @covers   Joomla\Router\Route
	 */
	public function testInstantiationWithMultiSegmentRouteWithVariable()
	{
		$route = new Route(['GET'], 'page/:page', 'page', [], ['_format' => 'html']);

		$this->assertSame(['GET'], $route->getMethods());
		$this->assertSame('page/:page', $route->getPattern());
		$this->assertSame('page', $route->getController());
		$this->assertSame([], $route->getRules());
		$this->assertSame(['_format' => 'html'], $route->getDefaults());
		$this->assertSame(\chr(1) . '^page/([^/]*)$' . \chr(1), $route->getRegex());
		$this->assertSame(['page'], $route->getRouteVariables());
	}

	/**
	 * @testdox  A route with a string controller can be serialized
	 *
	 * @covers   Joomla\Router\Route
	 */
	public function testSerialization()
	{
		$route = new Route(['GET'], 'login', 'login', [], ['_format' => 'html']);

		$unserializedRoute = unserialize(serialize($route));

		$this->assertNotSame($unserializedRoute, $route, 'A new router instance should be created when unserialized');

		$this->assertSame(['GET'], $unserializedRoute->getMethods());
		$this->assertSame('login', $unserializedRoute->getPattern());
		$this->assertSame('login', $unserializedRoute->getController());
		$this->assertSame([], $unserializedRoute->getRules());
		$this->assertSame(['_format' => 'html'], $unserializedRoute->getDefaults());
		$this->assertSame(\chr(1) . '^login$' . \chr(1), $unserializedRoute->getRegex());
		$this->assertSame([], $unserializedRoute->getRouteVariables());
	}

	/**
	 * @testdox  A route with a Closure controller can be serialized
	 *
	 * @covers   Joomla\Router\Route
	 */
	public function testSerializationWithClosure()
	{
		$route = new Route(['GET'], '/', function () {}, [], ['_format' => 'html']);

		$unserializedRoute = unserialize(serialize($route));

		$this->assertNotSame($unserializedRoute, $route, 'A new router instance should be created when unserialized');

		$this->assertSame(['GET'], $unserializedRoute->getMethods());
		$this->assertSame('/', $unserializedRoute->getPattern());
		$this->assertInstanceOf(SerializableClosure::class, $unserializedRoute->getController());
		$this->assertSame([], $unserializedRoute->getRules());
		$this->assertSame(['_format' => 'html'], $unserializedRoute->getDefaults());
		$this->assertSame(\chr(1) . '^$' . \chr(1), $unserializedRoute->getRegex());
		$this->assertSame([], $unserializedRoute->getRouteVariables());
	}
}

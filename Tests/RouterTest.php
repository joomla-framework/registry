<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Router\Tests;

use Joomla\Router\Exception\MethodNotAllowedException;
use Joomla\Router\Exception\RouteNotFoundException;
use Joomla\Router\Route;
use Joomla\Router\Router;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Router\Router class.
 */
class RouterTest extends TestCase
{
	/**
	 * An instance of the object to be tested.
	 *
	 * @var  Router
	 */
	protected $instance;

	/**
	 * {@inheritdoc}
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->instance = new Router;
	}

	/**
	 * @testdox  Ensure the Router is instantiated correctly with no injected routes.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function test__construct()
	{
		$this->assertEmpty(
			(new Router)->getRoutes(),
			'A Router should have no known routes by default.'
		);
	}

	/**
	 * @testdox  Ensure the Router is instantiated correctly with injected routes.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function test__constructNotEmpty()
	{
		$routes = [
			[
				'pattern'    => 'login',
				'controller' => 'login',
			],
			[
				'pattern'    => 'requests/:request_id',
				'controller' => 'request',
				'rules'      => [
					'request_id' => '(\d+)',
				],
			],
		];

		$rules = [
			new Route(['GET'], 'login', 'login', [], []),
			new Route(['GET'], 'requests/:request_id', 'request', ['request_id' => '(\d+)'], []),
		];

		$router = new Router($routes);

		$this->assertEquals(
			$rules,
			$router->getRoutes(),
			'When passing an array of routes when instantiating a Router, the maps property should be set accordingly.'
		);
	}

	/**
	 * @testdox  Ensure a route is added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testAddRoute()
	{
		$route = new Route(['GET'], 'foo', 'MyApplicationFoo', [], []);

		$this->assertSame(
			$this->instance,
			$this->instance->addRoute($route),
			'The addRoute method has a fluent interface'
		);

		$this->assertEquals(
			[
				$route,
			],
			$this->instance->getRoutes()
		);
	}

	/**
	 * @testdox  Ensure a route is added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testAddRouteWithDefaults()
	{
		$route = new Route(['GET'], 'foo', 'MyApplicationFoo', [], ['default1' => 'foo']);

		$this->assertSame(
			$this->instance,
			$this->instance->addRoute($route),
			'The addRoute method has a fluent interface'
		);

		$this->assertEquals(
			[
				$route,
			],
			$this->instance->getRoutes()
		);
	}

	/**
	 * @testdox  Ensure a GET route is added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testGet()
	{
		$this->assertSame(
			$this->instance,
			$this->instance->get('foo', 'MyApplicationFoo', [], ['default1' => 'foo']),
			'The get method has a fluent interface'
		);
	}

	/**
	 * @testdox  Ensure a POST route is added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testPost()
	{
		$this->assertSame(
			$this->instance,
			$this->instance->post('foo', 'MyApplicationFoo', [], ['default1' => 'foo']),
			'The post method has a fluent interface'
		);
	}

	/**
	 * @testdox  Ensure a PUT route is added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testPut()
	{
		$this->assertSame(
			$this->instance,
			$this->instance->put('foo', 'MyApplicationFoo', [], ['default1' => 'foo']),
			'The put method has a fluent interface'
		);
	}

	/**
	 * @testdox  Ensure a DELETE route is added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testDelete()
	{
		$this->assertSame(
			$this->instance,
			$this->instance->delete('foo', 'MyApplicationFoo', [], ['default1' => 'foo']),
			'The delete method has a fluent interface'
		);
	}

	/**
	 * @testdox  Ensure a HEAD route is added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testHead()
	{
		$this->assertSame(
			$this->instance,
			$this->instance->head('foo', 'MyApplicationFoo', [], ['default1' => 'foo']),
			'The head method has a fluent interface'
		);
	}

	/**
	 * @testdox  Ensure a OPTIONS route is added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testOptions()
	{
		$this->assertSame(
			$this->instance,
			$this->instance->options('foo', 'MyApplicationFoo', [], ['default1' => 'foo']),
			'The options method has a fluent interface'
		);
	}

	/**
	 * @testdox  Ensure a TRACE route is added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testTrace()
	{
		$this->assertSame(
			$this->instance,
			$this->instance->trace('foo', 'MyApplicationFoo', [], ['default1' => 'foo']),
			'The trace method has a fluent interface'
		);
	}

	/**
	 * @testdox  Ensure a PATCH route is added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testPatch()
	{
		$this->assertSame(
			$this->instance,
			$this->instance->patch('foo', 'MyApplicationFoo', [], ['default1' => 'foo']),
			'The patch method has a fluent interface'
		);
	}

	/**
	 * @testdox  Ensure a route supporting all methods is added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testAll()
	{
		$this->assertSame(
			$this->instance,
			$this->instance->all('foo', 'MyApplicationFoo', [], ['default1' => 'foo']),
			'The all method has a fluent interface'
		);
	}

	/**
	 * @testdox  Ensure several routes are added to the Router.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testAddRoutes()
	{
		$routes = [
			[
				'pattern'    => 'login',
				'controller' => 'login',
			],
			[
				'pattern'    => 'user/:name/:id',
				'controller' => 'UserController',
				'rules'      => [
					'id' => '(\d+)',
				],
			],
			[
				'pattern'    => 'requests/:request_id',
				'controller' => 'request',
				'rules'      => [
					'request_id' => '(\d+)',
				],
			],
		];

		$rules = [
			new Route(['GET'], 'login', 'login', [], []),
			new Route(['GET'], 'user/:name/:id', 'UserController', ['id' => '(\d+)'], []),
			new Route(['GET'], 'requests/:request_id', 'request', ['request_id' => '(\d+)'], []),
		];

		$this->assertSame(
			$this->instance,
			$this->instance->addRoutes($routes),
			'The addRoutes method has a fluent interface'
		);

		$this->assertEquals($rules, $this->instance->getRoutes());
	}

	/**
	 * @testdox  Ensure the Router parses routes.
	 *
	 * @param    string   $r  The route to parse.
	 * @param    boolean  $e  True if an exception is expected.
	 * @param    array    $i  The expected return data.
	 * @param    boolean  $m  True if routes should be set up.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\ResolvedRoute
	 * @uses     Joomla\Router\Route
	 *
	 * @dataProvider  seedTestParseRoute
	 */
	public function testParseRoute($r, $e, $i, $m)
	{
		if ($m)
		{
			$this->setRoutes();
		}

		// If we should expect an exception set that up.
		if ($e)
		{
			$this->expectException(RouteNotFoundException::class);
		}

		// Execute the route parsing.
		$actual = $this->instance->parseRoute($r);

		// Test the assertions.
		$this->assertSame($i['controller'], $actual->getController());
		$this->assertEquals($i['vars'], $actual->getRouteVariables());
	}

	/**
	 * @testdox  Ensure the Router handles a method not allowed error correctly.
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Exception\MethodNotAllowedException
	 * @uses     Joomla\Router\Route
	 */
	public function testParseRouteWithMethodNotAllowedError()
	{
		$this->expectException(MethodNotAllowedException::class);
		$this->expectExceptionMessage('Route `test/foo/path/bar` does not support `POST` requests.');

		$this->instance->get('test/foo/path/bar', 'TestController');

		// Execute the route parsing.
		$this->instance->parseRoute('test/foo/path/bar', 'POST');
	}

	/**
	 * @testdox  The router can be serialized
	 *
	 * @covers   Joomla\Router\Router
	 * @uses     Joomla\Router\Route
	 */
	public function testSerialization()
	{
		$this->setRoutes();

		$unserializedRouter = unserialize(serialize($this->instance));

		$this->assertNotSame($unserializedRouter, $this->instance, 'A new router instance should be created when unserialized');
		$this->assertEquals($unserializedRouter->getRoutes(), $this->instance->getRoutes(), 'The routers should have the same routes');
	}

	/**
	 * Provides test data for the testParseRoute method.
	 *
	 * @return  \Generator
	 */
	public function seedTestParseRoute(): \Generator
	{
		// Route Pattern, Throws Exception, Return Data, MapSetup
		yield ['', true, [], false];
		yield ['articles/4', true, [], false];
		yield ['', false, ['controller' => 'DefaultController', 'vars' => []], true];
		yield ['login', false, ['controller' => 'LoginController', 'vars' => []], true];
		yield ['articles', false, ['controller' => 'ArticlesController', 'vars' => []], true];
		yield ['articles/4', false, ['controller' => 'ArticleController', 'vars' => ['article_id' => 4]], true];
		yield ['articles/4/crap', true, [], true];
		yield ['test', true, [], true];
		yield ['test/foo', true, [], true];
		yield ['test/foo/path', true, [], true];
		yield ['test/foo/path/bar', false, ['controller' => 'TestController', 'vars' => ['seg1' => 'foo', 'seg2' => 'bar']], true];
		yield ['content/article-1/*', false, ['controller' => 'ContentController', 'vars' => []], true];

		yield [
			'content/cat-1/article-1',
			false,
			['controller' => 'ArticleController', 'vars' => ['category' => 'cat-1', 'article' => 'article-1']],
			true,
		];

		yield [
			'content/cat-1/cat-2/article-1',
			false,
			['controller' => 'ArticleController', 'vars' => ['category' => 'cat-1/cat-2', 'article' => 'article-1']],
			true,
		];

		yield [
			'content/cat-1/cat-2/cat-3/article-1',
			false,
			['controller' => 'ArticleController', 'vars' => ['category' => 'cat-1/cat-2/cat-3', 'article' => 'article-1']],
			true,
		];

		yield [
			'default_option/4',
			false,
			['controller' => 'ArticleController', 'vars' => ['article_id' => 4, 'option' => 'content']],
			true,
		];

		yield [
			'overriden_option/article/4',
			false,
			['controller' => 'ArticleController', 'vars' => ['id' => 4, 'option' => 'content', 'view' => 'article']],
			true,
		];
	}

	/**
	 * Setup the router with routes.
	 *
	 * @return  void
	 */
	protected function setRoutes(): void
	{
		$this->instance->addRoutes(
			[
				[
					'pattern'    => 'login',
					'controller' => 'LoginController',
				],
				[
					'pattern'    => 'logout',
					'controller' => 'LogoutController',
				],
				[
					'pattern'    => 'articles',
					'controller' => 'ArticlesController',
				],
				[
					'pattern'    => 'articles/:article_id',
					'controller' => 'ArticleController',
				],
				[
					'pattern'    => 'test/:seg1/path/:seg2',
					'controller' => 'TestController',
				],
				[
					'pattern'    => 'content/:/\*',
					'controller' => 'ContentController',
				],
				[
					'pattern'    => 'content/*category/:article',
					'controller' => 'ArticleController',
				],
				[
					'pattern'    => '/',
					'controller' => 'DefaultController',
				],
				[
					'pattern'    => 'default_option/:article_id',
					'controller' => 'ArticleController',
					'defaults'   => [
						'option' => 'content',
					],
				],
				[
					'pattern'    => 'overriden_option/:view/:id',
					'controller' => 'ArticleController',
					'defaults'   => [
						'option' => 'content',
						'view'   => 'category',
					],
				],
			]
		);
	}
}

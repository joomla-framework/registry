<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Router\Tests\Command;

use Joomla\Console\Application;
use Joomla\Router\Command\DebugRouterCommand;
use Joomla\Router\Route;
use Joomla\Router\Router;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Router\Command\DebugRouterCommand
 */
class DebugRouterCommandTest extends TestCase
{
	/**
	 * @testdox  The router's routes can be listed when the router is empty
	 *
	 * @covers   Joomla\Router\Command\DebugRouterCommand
	 * @uses     Joomla\Router\Route
	 * @uses     Joomla\Router\Router
	 */
	public function testTheCommandIsExecutedWithAnEmptyRouter()
	{
		$router = new Router;

		$input  = new ArrayInput(
			[
				'command' => 'debug:router',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new DebugRouterCommand($router);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The router has no routes.', $screenOutput);
	}

	/**
	 * @testdox  The router's routes can be listed when the router has routes
	 *
	 * @covers   Joomla\Router\Command\DebugRouterCommand
	 * @uses     Joomla\Router\Route
	 * @uses     Joomla\Router\Router
	 */
	public function testTheCommandIsExecutedWithAConfiguredRouter()
	{
		$router = new Router;
		$router->addRoute(new Route(['GET'], 'login', 'login', [], []));
		$router->addRoute(new Route(['POST'], 'login', 'submitLogin', [], []));
		$router->addRoute(new Route(['GET'], 'user/:name/:id', 'UserController', ['name' => '(\s+)', 'id' => '(\d+)'], []));
		$router->addRoute(new Route(['GET'], 'requests/:request_id', 'request', ['request_id' => '(\d+)'], []));

		$input  = new ArrayInput(
			[
				'command' => 'debug:router',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new DebugRouterCommand($router);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();

		$this->assertStringContainsString('user/:name/:id', $screenOutput);
		$this->assertStringNotContainsString('UserController', $screenOutput);
	}

	/**
	 * @testdox  The router's routes can be listed when the router has routes and the controllers are displayed
	 *
	 * @covers   Joomla\Router\Command\DebugRouterCommand
	 * @uses     Joomla\Router\Route
	 * @uses     Joomla\Router\Router
	 */
	public function testTheCommandIsExecutedWithAConfiguredRouterAndControllersAreDisplayed()
	{
		$router = new Router;
		$router->addRoute(new Route(['GET'], 'login', 'login', [], []));
		$router->addRoute(new Route(['POST'], 'login', 'submitLogin', [], []));
		$router->addRoute(new Route(['GET'], 'user/:name/:id', 'UserController', ['name' => '(\s+)', 'id' => '(\d+)'], []));
		$router->addRoute(new Route(['GET'], 'requests/:request_id', 'request', ['request_id' => '(\d+)'], []));

		$input  = new ArrayInput(
			[
				'command'            => 'debug:router',
				'--show-controllers' => true,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new DebugRouterCommand($router);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();

		$this->assertStringContainsString('user/:name/:id', $screenOutput);
		$this->assertStringContainsString('UserController', $screenOutput);
	}
}

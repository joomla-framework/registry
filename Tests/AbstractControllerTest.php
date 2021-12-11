<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Controller\Tests;

use Joomla\Application\AbstractApplication;
use Joomla\Controller\AbstractController;
use Joomla\Input\Input;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Controller\AbstractController class.
 */
class AbstractControllerTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  MockObject|AbstractController
	 */
	private $instance;

	/**
	 * {@inheritdoc}
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->instance = $this->getMockForAbstractClass(AbstractController::class);
	}

	/**
	 * @testdox  Tests the controller is instantiated correctly
	 *
	 * @covers   Joomla\Controller\AbstractController
	 */
	public function test__constructDefaultBehaviour()
	{
		$this->assertNull($this->instance->getApplication());
		$this->assertNull($this->instance->getInput());
	}

	/**
	 * @testdox  Tests the controller is instantiated correctly
	 *
	 * @covers   Joomla\Controller\AbstractController
	 */
	public function test__constructDependencyInjection()
	{
		$mockInput = $this->createMock(Input::class);

		$mockApp = $this->getMockForAbstractClass(AbstractApplication::class);
		$object  = $this->getMockForAbstractClass(AbstractController::class, [$mockInput, $mockApp]);

		$this->assertSame($mockApp, $object->getApplication());
		$this->assertSame($mockInput, $object->getInput());
	}

	/**
	 * @testdox  Tests an application object is injected into the controller and retrieved correctly
	 *
	 * @covers   Joomla\Controller\AbstractController
	 */
	public function testSetAndGetApplication()
	{
		$mockApp = $this->getMockForAbstractClass(AbstractApplication::class);

		$this->assertSame($this->instance, $this->instance->setApplication($mockApp), 'The setApplication method has a fluent interface');
		$this->assertSame($mockApp, $this->instance->getApplication());
	}

	/**
	 * @testdox  Tests an input object is injected into the controller and retrieved correctly
	 *
	 * @covers   Joomla\Controller\AbstractController
	 */
	public function testSetAndGetInput()
	{
		$mockInput = $this->createMock(Input::class);

		$this->assertSame($this->instance, $this->instance->setInput($mockInput), 'The setInput method has a fluent interface');
		$this->assertSame($mockInput, $this->instance->getInput());
	}
}

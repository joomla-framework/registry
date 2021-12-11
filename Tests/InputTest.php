<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Input\Tests;

use Joomla\Filter\InputFilter;
use Joomla\Input\Input;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Input\Input.
 */
class InputTest extends TestCase
{
	/**
	 * The test class.
	 *
	 * @var  Input
	 */
	private $instance;

	/**
	 * The mock filter object
	 *
	 * @var  InputFilter|MockObject
	 */
	private $filterMock;

	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->filterMock = $this->createMock(InputFilter::class);
	}

	/**
	 * Get an Input object populated with passed in data
	 *
	 * @param   array  $data  Optional source data. If omitted, a copy of the server variable '_REQUEST' is used.
	 *
	 * @return  Input
	 */
	protected function getInputObject($data = null)
	{
		return new Input($data, ['filter' => $this->filterMock]);
	}

	/**
	 * @testdox  Tests the default constructor behavior
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function test__constructDefaultBehaviour()
	{
		$instance = new Input;

		$this->assertSame($_REQUEST, TestHelper::getValue($instance, 'data'), 'The Input input defaults to the $_REQUEST superglobal');
		$this->assertInstanceOf(InputFilter::class, TestHelper::getValue($instance, 'filter'), 'The Input object should create an InputFilter if one is not provided');
	}

	/**
	 * @testdox  Tests the constructor with injected data
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function test__constructDependencyInjection()
	{
		$instance = $this->getInputObject($_GET);

		$this->assertSame($_GET, TestHelper::getValue($instance, 'data'));
		$this->assertSame($this->filterMock, TestHelper::getValue($instance, 'filter'));
	}

	/**
	 * @testdox  Tests convenience methods are proxied
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function test__callProxiesToTheGetMethod()
	{
		$this->filterMock->expects($this->once())
			->method('clean')
			->willReturnArgument(0);

		$instance = $this->getInputObject(['foo' => 'bar']);

		$this->assertSame('bar', $instance->getRaw('foo'));
	}

	/**
	 * @testdox  Tests an error is thrown if an undefined method is called
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function test__callThrowsAnErrorIfAnUndefinedMethodIsCalled()
	{
		$this->expectError();

		$instance = $this->getInputObject()->setRaw();
	}

	/**
	 * @testdox   Tests the magic get method correctly proxies to another global
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function test__get()
	{
		$instance = $this->getInputObject();

		$this->assertSame($_GET, TestHelper::getValue($instance->get, 'data'));
		$this->assertArrayHasKey('get', TestHelper::getValue($instance, 'inputs'), 'An object retrieved via __get() should be cached internally');
	}

	/**
	 * @testdox   Tests the magic get method correctly proxies to another global represented by the Input class and returns the same instance
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function test__getCachedInstances()
	{
		$instance = $this->getInputObject();

		$this->assertSame($instance->get, $instance->get, 'The same Input instance should be returned');
	}

	/**
	 * @testdox   Tests the magic get method correctly proxies to another global represented by a Input subclass and returns the same instance
	 *
	 * @covers   Joomla\Input\Input
	 * @uses     Joomla\Input\Files
	 */
	public function test__getCachedInstancesSubclasses()
	{
		$instance = $this->getInputObject();

		$this->assertSame($instance->files, $instance->files, 'The same Files instance should be returned');
	}

	/**
	 * @testdox   Tests an error is thrown if an undefined property is called
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function test__getThrowsAnErrorIfAnUndefinedPropertyIsCalled()
	{
		$this->expectError();

		$instance = $this->getInputObject()->put;
	}

	/**
	 * @testdox   Tests the data store is counted
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function testCount()
	{
		$this->assertCount(3, $this->getInputObject(['foo' => 2, 'bar' => 3, 'gamma' => 4]));
	}

	/**
	 * @testdox  Tests the data source is correctly read
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function testGet()
	{
		$this->filterMock->expects($this->once())
			->method('clean')
			->willReturnArgument(0);

		$instance = $this->getInputObject(['foo' => 'bar']);

		$this->assertEquals('bar', $instance->get('foo'));
	}

	/**
	 * @testdox  Tests a key is not redefined if already present
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function testDefNotReadWhenValueExists()
	{
		$this->filterMock->expects($this->once())
			->method('clean')
			->willReturnArgument(0);

		$instance = $this->getInputObject(['foo' => 'bar']);

		$instance->def('foo', 'nope');

		$this->assertEquals('bar', $instance->get('foo'));
	}

	/**
	 * @testdox  Tests a key is defined when not present
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function testDefRead()
	{
		$this->filterMock->expects($this->once())
			->method('clean')
			->willReturnArgument(0);

		$instance = $this->getInputObject(['foo' => 'bar']);

		$instance->def('bar', 'nope');

		$this->assertEquals('nope', $instance->get('bar'));
	}

	/**
	 * @testdox  Tests a key is added or overwritten in the data source
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function testSet()
	{
		$this->filterMock->expects($this->once())
			->method('clean')
			->willReturnArgument(0);

		$instance = $this->getInputObject(['foo' => 'bar']);

		$instance->set('foo', 'gamma');

		$this->assertEquals('gamma', $instance->get('foo'));
	}

	/**
	 * @testdox  Tests for a key's existence in the data source
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function testExists()
	{
		$instance = $this->getInputObject(['foo' => 'bar']);

		$this->assertTrue($instance->exists('foo'));
	}

	/**
	 * @testdox  Tests that an array of keys are read from the data source
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function testGetArray()
	{
		$this->filterMock->expects($this->any())
			->method('clean')
			->willReturnArgument(0);

		$array = [
			'var1' => 'value1',
			'var2' => 34,
			'var3' => ['test'],
			'var4' => ['var1' => ['var2' => 'test']]
		];

		$input = $this->getInputObject($array);

		$this->assertEquals(
			$array,
			$input->getArray(
				['var1' => 'string', 'var2' => 'int', 'var3' => 'array', 'var4' => ['var1' => ['var2' => 'array']]]
			)
		);
	}

	/**
	 * @testdox  Tests that the full data array is read from the data source
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function testGetArrayWithoutSpecifiedVariables()
	{
		$this->filterMock->expects($this->any())
			->method('clean')
			->willReturnArgument(0);

		$array = [
			'var2' => 34,
			'var3' => ['var2' => 'test'],
			'var4' => ['var1' => ['var2' => 'test']],
			'var5' => ['foo' => []],
			'var6' => ['bar' => null],
			'var7' => null
		];

		$input = $this->getInputObject($array);

		$this->assertEquals($input->getArray(), $array);
	}

	/**
	 * @testdox  Tests that the request method is returned
	 *
	 * @covers   Joomla\Input\Input
	 *
	 * @backupGlobals enabled
	 */
	public function testGetMethod()
	{
		$this->filterMock->expects($this->any())
			->method('clean')
			->willReturnArgument(0);

		$_SERVER['REQUEST_METHOD'] = 'custom';

		$instance = $this->getInputObject();

		$this->assertEquals('CUSTOM', $instance->getMethod());
	}

	/**
	 * @testdox  Tests that the Input object for the request method is returned on a GET request
	 *
	 * @covers   Joomla\Input\Input
	 *
	 * @backupGlobals enabled
	 */
	public function testGetInputForRequestMethodWithGetRequest()
	{
		$this->filterMock->expects($this->any())
			->method('clean')
			->willReturnArgument(0);

		$_SERVER['REQUEST_METHOD'] = 'GET';

		$instance = $this->getInputObject();

		$this->assertInstanceOf(Input::class, $instance->getInputForRequestMethod());
		$this->assertSame($instance->get, $instance->getInputForRequestMethod(), 'A request method that does have its own superglobal returns the Input object for that global');
	}

	/**
	 * @testdox  Tests that the Input object for the request method is returned on a POST request
	 *
	 * @covers   Joomla\Input\Input
	 *
	 * @backupGlobals enabled
	 */
	public function testGetInputForRequestMethodWithPostRequest()
	{
		$this->filterMock->expects($this->any())
			->method('clean')
			->willReturnArgument(0);

		$_SERVER['REQUEST_METHOD'] = 'POST';

		$instance = $this->getInputObject();

		$this->assertInstanceOf(Input::class, $instance->getInputForRequestMethod());
		$this->assertSame($instance->post, $instance->getInputForRequestMethod(), 'A request method that does have its own superglobal returns the Input object for that global');
	}

	/**
	 * @testdox  Tests that the Input object for the request method is returned on a PUT request
	 *
	 * @covers   Joomla\Input\Input
	 *
	 * @backupGlobals enabled
	 */
	public function testGetInputForRequestMethodWithPutRequest()
	{
		$this->filterMock->expects($this->any())
			->method('clean')
			->willReturnArgument(0);

		$_SERVER['REQUEST_METHOD'] = 'PUT';

		$instance = $this->getInputObject();

		$this->assertInstanceOf(Input::class, $instance->getInputForRequestMethod());
		$this->assertSame($instance, $instance->getInputForRequestMethod(), 'A request method that does not have its own superglobal returns the current Input object');
	}

	/**
	 * @testdox  Tests that the get method disallows access to non-whitelisted globals
	 *
	 * @covers   Joomla\Input\Input
	 */
	public function testGetDoesNotSupportNonWhitelistedGlobals()
	{
		$this->markTestSkipped('Update to account for notice being raised.');

		$this->assertNull(
			$this->getInputObject()->_phpunit_configuration_file,
			'Access to library defined globals is restricted'
		);
	}
}

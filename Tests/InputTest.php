<?php
/**
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
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
	 * @param   array|null  $data  Optional source data. If omitted, a copy of the server variable '_REQUEST' is used.
	 *
	 * @return  Input
	 */
	protected function getInputObject(?array $data = null): Input
	{
		return new Input($data, ['filter' => $this->filterMock]);
	}

	/**
	 * @testdox  Default constructor behavior
	 *
	 * @covers   \Joomla\Input\Input
	 * @throws \ReflectionException
	 */
	public function test__constructDefaultBehaviour(): void
	{
		$instance = new Input;

		$this->assertSame($_REQUEST, TestHelper::getValue($instance, 'data'), 'The Input input defaults to the $_REQUEST superglobal');
		$this->assertInstanceOf(InputFilter::class, TestHelper::getValue($instance, 'filter'), 'The Input object should create an InputFilter if one is not provided');
	}

	/**
	 * @testdox  Constructor with injected data
	 *
	 * @covers   \Joomla\Input\Input
	 * @throws \ReflectionException
	 */
	public function test__constructDependencyInjection(): void
	{
		$instance = $this->getInputObject($_GET);

		$this->assertSame($_GET, TestHelper::getValue($instance, 'data'));
		$this->assertSame($this->filterMock, TestHelper::getValue($instance, 'filter'));
	}

	/**
	 * @testdox  Convenience methods are proxied
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function test__callProxiesToTheGetMethod(): void
	{
		$this->filterMock->expects($this->once())
			->method('clean')
			->willReturnArgument(0);

		$instance = $this->getInputObject(['foo' => 'bar']);

		$this->assertSame('bar', $instance->getRaw('foo'));
	}

	/**
	 * @testdox  An error is thrown if an undefined method is called
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function test__callThrowsAnErrorIfAnUndefinedMethodIsCalled(): void
	{
		$this->expectError();

		/** @noinspection PhpUndefinedMethodInspection */
		$this->getInputObject()->setRaw();
	}

	/**
	 * @testdox   Magic get method correctly proxies to another global
	 *
	 * @covers    \Joomla\Input\Input
	 * @throws \ReflectionException
	 */
	public function test__get(): void
	{
		$instance = $this->getInputObject();

		$this->assertSame($_GET, TestHelper::getValue($instance->get, 'data'));
		$this->assertArrayHasKey('get', TestHelper::getValue($instance, 'inputs'), 'An object retrieved via __get() should be cached internally');
	}

	/**
	 * @testdox   Magic get method correctly proxies to another global represented by the Input class and returns the same instance
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function test__getCachedInstances(): void
	{
		$instance = $this->getInputObject();

		$this->assertSame($instance->get, $instance->get, 'The same Input instance should be returned');
	}

	/**
	 * @testdox   Magic get method correctly proxies to another global represented by an Input subclass and returns the same instance
	 *
	 * @covers   \Joomla\Input\Input
	 * @uses     \Joomla\Input\Files
	 */
	public function test__getCachedInstancesSubclasses(): void
	{
		$instance = $this->getInputObject();

		$this->assertSame($instance->files, $instance->files, 'The same Files instance should be returned');
	}

	/**
	 * @testdox   An error is thrown if an undefined property is called
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function test__getThrowsAnErrorIfAnUndefinedPropertyIsCalled(): void
	{
		$this->expectError();

		/** @noinspection PhpUndefinedFieldInspection */
		$this->getInputObject()->put;
	}

	/**
	 * @testdox   Data store is counted
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function testCount(): void
	{
		$this->assertCount(3, $this->getInputObject(['foo' => 2, 'bar' => 3, 'gamma' => 4]));
	}

	/**
	 * @testdox  Data source is correctly read
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function testGet(): void
	{
		$this->filterMock->expects($this->once())
			->method('clean')
			->willReturnArgument(0);

		$instance = $this->getInputObject(['foo' => 'bar']);

		$this->assertEquals('bar', $instance->get('foo'));
	}

	/**
	 * @testdox  A key is not redefined if already present
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function testDefNotReadWhenValueExists(): void
	{
		$this->filterMock->expects($this->once())
			->method('clean')
			->willReturnArgument(0);

		$instance = $this->getInputObject(['foo' => 'bar']);

		$instance->def('foo', 'nope');

		$this->assertEquals('bar', $instance->get('foo'));
	}

	/**
	 * @testdox  A key is defined when not present
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function testDefRead(): void
	{
		$this->filterMock->expects($this->once())
			->method('clean')
			->willReturnArgument(0);

		$instance = $this->getInputObject(['foo' => 'bar']);

		$instance->def('bar', 'nope');

		$this->assertEquals('nope', $instance->get('bar'));
	}

	/**
	 * @testdox  A key is added or overwritten in the data source
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function testSet(): void
	{
		$this->filterMock->expects($this->once())
			->method('clean')
			->willReturnArgument(0);

		$instance = $this->getInputObject(['foo' => 'bar']);

		$instance->set('foo', 'gamma');

		$this->assertEquals('gamma', $instance->get('foo'));
	}

	/**
	 * @testdox  For a key's existence in the data source
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function testExists(): void
	{
		$instance = $this->getInputObject(['foo' => 'bar']);

		$this->assertTrue($instance->exists('foo'));
	}

	/**
	 * @testdox  An array of keys is read from the data source
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function testGetArray(): void
	{
		$this->filterMock
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
	 * @testdox  Full data array is read from the data source
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function testGetArrayWithoutSpecifiedVariables(): void
	{
		$this->filterMock
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
	 * @testdox  Request method is returned
	 *
	 * @covers   \Joomla\Input\Input
	 *
	 * @backupGlobals enabled
	 */
	public function testGetMethod(): void
	{
		$this->filterMock
			->method('clean')
			->willReturnArgument(0);

		$_SERVER['REQUEST_METHOD'] = 'custom';

		$instance = $this->getInputObject();

		$this->assertEquals('CUSTOM', $instance->getMethod());
	}

	/**
	 * @testdox  Input object for the request method is returned on a GET request
	 *
	 * @covers   \Joomla\Input\Input
	 *
	 * @backupGlobals enabled
	 */
	public function testGetInputForRequestMethodWithGetRequest(): void
	{
		$this->filterMock
			->method('clean')
			->willReturnArgument(0);

		$_SERVER['REQUEST_METHOD'] = 'GET';

		$instance = $this->getInputObject();

		$this->assertInstanceOf(Input::class, $instance->getInputForRequestMethod());
		$this->assertSame($instance->get, $instance->getInputForRequestMethod(), 'A request method that does have its own superglobal returns the Input object for that global');
	}

	/**
	 * @testdox  Input object for the request method is returned on a POST request
	 *
	 * @covers   \Joomla\Input\Input
	 *
	 * @backupGlobals enabled
	 */
	public function testGetInputForRequestMethodWithPostRequest(): void
	{
		$this->filterMock
			->method('clean')
			->willReturnArgument(0);

		$_SERVER['REQUEST_METHOD'] = 'POST';

		$instance = $this->getInputObject();

		$this->assertInstanceOf(Input::class, $instance->getInputForRequestMethod());
		$this->assertSame($instance->post, $instance->getInputForRequestMethod(), 'A request method that does have its own superglobal returns the Input object for that global');
	}

	/**
	 * @testdox  Input object for the request method is returned on a PUT request
	 *
	 * @covers   \Joomla\Input\Input
	 *
	 * @backupGlobals enabled
	 */
	public function testGetInputForRequestMethodWithPutRequest(): void
	{
		$this->filterMock
			->method('clean')
			->willReturnArgument(0);

		$_SERVER['REQUEST_METHOD'] = 'PUT';

		$instance = $this->getInputObject();

		$this->assertInstanceOf(Input::class, $instance->getInputForRequestMethod());
		$this->assertSame($instance, $instance->getInputForRequestMethod(), 'A request method that does not have its own superglobal returns the current Input object');
	}

	/**
	 * @testdox  Get method disallows access to non-whitelisted globals
	 *
	 * @covers   \Joomla\Input\Input
	 */
	public function testGetDoesNotSupportNonWhitelistedGlobals(): void
	{
		$this->expectError();
		$this->getInputObject()->_phpunit_configuration_file;
	}

	public function constructorCases(): \Generator
	{
		yield 'no source' => [
			'constructor-arg' => null,
			'expected' => 'value',
		];

		yield 'empty source' => [
			'constructor-arg' => [],
			'expected' => null,
		];

		yield 'non-empty source' => [
			'constructor-arg' => ['foo' => 'bar'],
			'expected' => null,
		];

		yield 'same key' => [
			'constructor-arg' => ['var' => 'bar'],
			'expected' => 'bar',
		];
	}

	/**
	 * @testdox If no source is provided ($source === null), $_REQUEST is used. If any source is provided ($source !== null), $_REQUEST is ignored.
	 *
	 * @dataProvider constructorCases
	 * @return void
	 *
	 * @backupGlobals enabled
	 */
	public function testConstructorUsesRequestIfNeeded($constructorArgs, $expected): void
	{
		$_REQUEST = ['var' => 'value'];

		$input = new Input($constructorArgs);

		$this->assertEquals($expected, $input->get('var'));
	}

	/**
	 * @testdox   Input object for the request method GET is not polluted with POST data
	 *
	 * @covers    \Joomla\Input\Input
	 *
	 * @backupGlobals enabled
	 */
	public function testGetRequestForPostData(): void
	{
		$_POST    = ['polluted' => '1'];
		$_GET     = [];
		$_REQUEST = array_merge($_GET, $_POST);

		$input = new Input($_GET);

		$this->assertEquals(0, $input->get->count(), 'get is being polluted by the post!');
	}

	/**
	 * @testdox  Input object for the request method POST is not polluted with GET data
	 *
	 * @covers   \Joomla\Input\Input
	 *
	 * @backupGlobals enabled
	 */
	public function testPostRequestForGetData(): void
	{
		$_GET     = ['polluted' => '1'];
		$_POST    = [];
		$_REQUEST = array_merge($_GET, $_POST);

		$input = new Input($_POST);

		$this->assertEquals(0, $input->post->count(), 'post is being polluted by the get!');
	}

	/**
	 * @testdox  GET and POST data are kept separate
	 *
	 * @covers   \Joomla\Input\Input
	 *
	 * @backupGlobals enabled
	 */
	public function testRequestFromGlobals(): void
	{
		$_GET     = ['1' => '1', '2' => '2', '3' => '3'];
		$_POST    = ['1' => '1', '2' => '2'];
		$_REQUEST = array_merge($_GET, $_POST);

		$input = new Input();

		$this->assertEquals(
			3,
			$input->get->count(),
			'Wrong number of items found in the $_GET in the input object when loading from GLOBALS'
		);
		$this->assertEquals(
			2,
			$input->post->count(),
			'Wrong number of items found in the $_POST in the input object when loading from GLOBALS'
		);
	}}

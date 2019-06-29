<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Input\Tests;

use Joomla\Filter\InputFilter;
use Joomla\Input\Input;
use PHPUnit\Framework\Error\Error;
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
	 * @covers   Joomla\Input\Input::__construct
	 */
	public function test__constructDefaultBehaviour()
	{
		$instance = new Input;

		$this->assertAttributeSame($_REQUEST, 'data', $instance);
		$this->assertAttributeInstanceOf(InputFilter::class, 'filter', $instance);
	}

	/**
	 * @testdox  Tests the constructor with injected data
	 *
	 * @covers   Joomla\Input\Input::__construct
	 */
	public function test__constructDependencyInjection()
	{
		$instance = $this->getInputObject($_GET);

		$this->assertAttributeSame($_GET, 'data', $instance);
		$this->assertAttributeSame($this->filterMock, 'filter', $instance);
	}

	/**
	 * @testdox  Tests convenience methods are proxied
	 *
	 * @covers   Joomla\Input\Input::__call
	 * @uses     Joomla\Input\Input::get
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
	 * @covers   Joomla\Input\Input::__call
	 */
	public function test__callThrowsAnErrorIfAnUndefinedMethodIsCalled()
	{
		$this->expectException(Error::class);

		$instance = $this->getInputObject()->setRaw();
	}

	/**
	 * @testdox   Tests the magic get method correctly proxies to another global
	 *
	 * @covers    Joomla\Input\Input::__get
	 */
	public function test__get()
	{
		$instance = $this->getInputObject();

		$this->assertAttributeEquals($_GET, 'data', $instance->get);
		$this->assertAttributeContains($instance->get, 'inputs', $instance, 'An object retrieved via __get() should be cached internally');
	}

	/**
	 * @testdox   Tests an error is thrown if an undefined property is called
	 *
	 * @covers    Joomla\Input\Input::__get
	 */
	public function test__getThrowsAnErrorIfAnUndefinedPropertyIsCalled()
	{
		$this->expectException(Error::class);

		$instance = $this->getInputObject()->put;
	}

	/**
	 * @testdox   Tests the data store is counted
	 *
	 * @covers    Joomla\Input\Input::count
	 */
	public function testCount()
	{
		$this->assertCount(3, $this->getInputObject(['foo' => 2, 'bar' => 3, 'gamma' => 4]));
	}

	/**
	 * @testdox  Tests the data source is correctly read
	 *
	 * @covers   Joomla\Input\Input::get
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
	 * @covers   Joomla\Input\Input::def
	 * @uses     Joomla\Input\Input::get
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
	 * @covers   Joomla\Input\Input::def
	 * @uses     Joomla\Input\Input::get
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
	 * @covers   Joomla\Input\Input::set
	 * @uses     Joomla\Input\Input::get
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
	 * @covers   Joomla\Input\Input::exists
	 */
	public function testExists()
	{
		$instance = $this->getInputObject(['foo' => 'bar']);

		$this->assertTrue($instance->exists('foo'));
	}

	/**
	 * @testdox  Tests that an array of keys are read from the data source
	 *
	 * @covers   Joomla\Input\Input::getArray
	 * @uses     Joomla\Input\Input::get
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
	 * @covers   Joomla\Input\Input::getArray
	 * @uses     Joomla\Input\Input::get
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
	 * @covers   Joomla\Input\Input::getMethod
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
	 * @testdox  Tests that the object is correctly serialized
	 *
	 * @covers   Joomla\Input\Input::loadAllInputs
	 * @covers   Joomla\Input\Input::serialize
	 *
	 * @backupGlobals enabled
	 */
	public function testSerialize()
	{
		$instance = $this->getInputObject();

		$this->assertGreaterThan(0, strlen($instance->serialize()));
		$this->assertAttributeEquals(true, 'loaded', $instance);
	}

	/**
	 * @testdox  Tests that the object is correctly unserialized
	 *
	 * @covers   Joomla\Input\Input::unserialize
	 */
	public function testUnserialize()
	{
		$serialized = 'a:3:{i:0;a:1:{s:6:"filter";s:3:"raw";}i:1;s:4:"data";i:2;a:1:{s:7:"request";s:4:"keep";}}';

		$instance = $this->getInputObject();

		$instance->unserialize($serialized);

		$this->assertAttributeSame(['request' => 'keep'], 'inputs', $instance);
		$this->assertAttributeSame(['filter' => 'raw'], 'options', $instance);
		$this->assertAttributeSame('data', 'data', $instance);
	}

	/**
	 * Test the JInput::get method disallows access to non-whitelisted globals.
	 *
	 * @return  void
	 *
	 * @since   1.3.0
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

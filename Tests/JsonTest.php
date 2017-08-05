<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Input\Tests;

use Joomla\Filter\InputFilter;
use Joomla\Input\Json;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Input\Json.
 */
class JsonTest extends TestCase
{
	/**
	 * @testdox  Tests the default constructor behavior
	 *
	 * @covers   Joomla\Input\Json::__construct
	 */
	public function test__constructDefaultBehaviour()
	{
		$instance = new Json;

		$this->assertAttributeEmpty('data', $instance);
		$this->assertAttributeInstanceOf('Joomla\Filter\InputFilter', 'filter', $instance);
	}

	/**
	 * @testdox  Tests the constructor with injected data
	 *
	 * @covers   Joomla\Input\Json::__construct
	 */
	public function test__constructDependencyInjection()
	{
		$src        = ['foo' => 'bar'];
		$mockFilter = $this->getMockBuilder(InputFilter::class)->getMock();

		$instance = new Json($src, ['filter' => $mockFilter]);

		$this->assertAttributeSame($src, 'data', $instance);
		$this->assertAttributeSame($mockFilter, 'filter', $instance);
	}

	/**
	 * @testdox  Tests the constructor when reading data from the $GLOBALS
	 *
	 * @covers   Joomla\Input\Json::__construct
	 *
	 * @backupGlobals enabled
	 */
	public function test__constructReadingFromGlobals()
	{
		$GLOBALS['HTTP_RAW_POST_DATA'] = '{"a":1,"b":2}';

		$instance = new Json;

		$this->assertAttributeSame(['a' => 1, 'b' => 2], 'data', $instance);
	}

	/**
	 * @testdox  Tests the constructor when reading data from the $GLOBALS
	 *
	 * @covers   Joomla\Input\Json::getRaw
	 * @uses     Joomla\Input\Json::__construct
	 *
	 * @backupGlobals enabled
	 */
	public function testgetRaw()
	{
		$GLOBALS['HTTP_RAW_POST_DATA'] = '{"a":1,"b":2}';

		$this->assertSame($GLOBALS['HTTP_RAW_POST_DATA'], (new Json)->getRaw());
	}
}

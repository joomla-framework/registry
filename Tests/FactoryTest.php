<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests;

use Joomla\Registry\Factory;
use PHPUnit\Framework\TestCase;
use Joomla\Registry\Format\Json;
use Joomla\Registry\Format\Ini;

/**
 * Test class for Joomla\Registry\Factory
 */
class FactoryTest extends TestCase
{
	/**
	 * @testdox  A format object is returned from the local Joomla namespace
	 *
	 * @covers   Joomla\Registry\Factory
	 */
	public function testGetFormatFromLocalNamespace()
	{
		$this->assertInstanceOf(
			Ini::class,
			Factory::getFormat('ini')
		);
	}

	/**
	 * @testdox  A format object is returned from the requested namespace
	 *
	 * @covers   Joomla\Registry\Factory
	 */
	public function testGetFormatFromRequestedNamespace()
	{
		$this->assertInstanceOf(
			Stubs\Ini::class,
			Factory::getFormat('ini', ['format_namespace' => __NAMESPACE__ . '\\Stubs'])
		);
	}

	/**
	 * @testdox  A format object is returned from the local namespace when not found in the requested namespace
	 *
	 * @covers   Joomla\Registry\Factory
	 */
	public function testGetFormatFromLocalNamespaceWhenRequestedNamespaceDoesNotExist()
	{
		$this->assertInstanceOf(
			Json::class,
			Factory::getFormat('json', ['format_namespace' => __NAMESPACE__ . '\\Stubs'])
		);
	}

	/**
	 * @testdox  An exception is thrown if the requested format does not exist
	 *
	 * @covers   Joomla\Registry\Factory
	 */
	public function testGetInstanceNonExistent()
	{
		$this->expectException(\InvalidArgumentException::class);

		Factory::getFormat('sql');
	}
}

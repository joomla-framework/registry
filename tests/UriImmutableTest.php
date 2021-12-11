<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri\Tests;

use Joomla\Uri\UriImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Uri\UriImmutable class.
 */
class UriImmutableTest extends TestCase
{
	/**
	 * Object under test
	 *
	 * @var  UriImmutable
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		$this->object = new UriImmutable('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');
	}

	public function test__set()
	{
		$this->expectException(\BadMethodCallException::class);

		$this->object->uri = 'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment';
	}
}

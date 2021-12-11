<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri\Tests;

use Joomla\Uri\AbstractUri;
use Joomla\Uri\UriImmutable;
use Joomla\Uri\UriInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Uri\AbstractUri class.
 */
class AbstractUriTest extends TestCase
{
	/**
	 * Object under test
	 *
	 * @var  MockObject|AbstractUri
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
		$this->object = $this->getMockForAbstractClass(
			AbstractUri::class,
			['http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment']
		);
	}

	public function test__toString()
	{
		$this->assertEquals(
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			(string) $this->object
		);
	}

	public function testToString()
	{
		// The next 2 tested functions should generate equivalent results
		$this->assertEquals(
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			$this->object->toString()
		);

		$this->assertEquals(
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			$this->object->toString(['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'])
		);

		$this->assertEquals(
			'http://',
			$this->object->toString(['scheme'])
		);

		$this->assertEquals(
			'www.example.com:80',
			$this->object->toString(['host', 'port'])
		);

		$this->assertEquals(
			'/path/file.html?var=value#fragment',
			$this->object->toString(['path', 'query', 'fragment'])
		);

		$this->assertEquals(
			'someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			$this->object->toString(['user', 'pass', 'host', 'port', 'path', 'query', 'fragment'])
		);
	}

	public function testRender()
	{
		$this->assertEquals(
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			$this->object->render(UriInterface::ALL)
		);

		$this->assertEquals(
			'http://',
			$this->object->render(UriInterface::SCHEME)
		);

		$this->assertEquals(
			'www.example.com:80',
			$this->object->render(UriInterface::HOST | UriInterface::PORT)
		);

		$this->assertEquals(
			'/path/file.html?var=value#fragment',
			$this->object->render(UriInterface::PATH | UriInterface::QUERY | UriInterface::FRAGMENT)
		);

		$this->assertEquals(
			'someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			$this->object->render(UriInterface::ALL & ~UriInterface::SCHEME)
		);
	}

	public function testHasVar()
	{
		$this->assertFalse($this->object->hasVar('somevar'));

		$this->assertTrue($this->object->hasVar('var'));
	}

	public function testGetVar()
	{
		$this->assertEquals(
			'value',
			$this->object->getVar('var')
		);

		$this->assertEquals(
			'',
			$this->object->getVar('var2')
		);

		$this->assertEquals(
			'default',
			$this->object->getVar('var2', 'default')
		);
	}

	public function testGetQuery()
	{
		$this->assertEquals(
			'var=value',
			$this->object->getQuery()
		);

		$this->assertEquals(
			['var' => 'value'],
			$this->object->getQuery(true)
		);
	}

	public function testGetScheme()
	{
		$this->assertEquals(
			'http',
			$this->object->getScheme()
		);
	}

	public function testGetUser()
	{
		$this->assertEquals(
			'someuser',
			$this->object->getUser()
		);
	}

	public function testGetPass()
	{
		$this->assertEquals(
			'somepass',
			$this->object->getPass()
		);
	}

	public function testGetHost()
	{
		$this->assertEquals(
			'www.example.com',
			$this->object->getHost()
		);
	}

	public function testGetPort()
	{
		$this->assertEquals(
			'80',
			$this->object->getPort()
		);
	}

	public function testGetPath()
	{
		$this->assertEquals(
			'/path/file.html',
			$this->object->getPath()
		);
	}

	public function testGetFragment()
	{
		$this->assertEquals(
			'fragment',
			$this->object->getFragment()
		);
	}

	public function testisSsl()
	{
		$this->assertTrue(
			(new UriImmutable('https://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment'))->isSsl()
		);

		$this->assertFalse(
			(new UriImmutable('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment'))->isSsl()
		);
	}
}

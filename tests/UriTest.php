<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri\Tests;

use Joomla\Uri\Uri;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Uri\Uri class.
 *
 * @since  1.0
 */
class UriTest extends TestCase
{
	/**
	 * Object under test
	 *
	 * @var    Uri
	 * @since  1.0
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp(): void
	{
		$this->object = new Uri('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');
	}

	public function test__toString()
	{
		$this->assertEquals(
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			(string) $this->object
		);
	}

	public function testConstruct()
	{
		$object = new Uri('http://someuser:somepass@www.example.com:80/path/file.html?var=value&amp;test=true#fragment');

		$this->assertEquals(
			'www.example.com',
			$object->getHost()
		);

		$this->assertEquals(
			'/path/file.html',
			$object->getPath()
		);

		$this->assertEquals(
			'http',
			$object->getScheme()
		);
	}

	public function testParseForBadUrl()
	{
		$this->expectException(\RuntimeException::class);

		new Uri('http:///www.example.com');
	}

	public function testToString()
	{
		$this->assertEquals(
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			$this->object->toString()
		);

		$this->object->setQuery('somevar=somevalue');
		$this->object->setVar('somevar2', 'somevalue2');
		$this->object->setScheme('ftp');
		$this->object->setUser('root');
		$this->object->setPass('secret');
		$this->object->setHost('www.example.org');
		$this->object->setPort('8888');
		$this->object->setFragment('someFragment');
		$this->object->setPath('/this/is/a/path/to/a/file');

		$this->assertEquals(
			'ftp://root:secret@www.example.org:8888/this/is/a/path/to/a/file?somevar=somevalue&somevar2=somevalue2#someFragment',
			$this->object->toString()
		);
	}

	public function testSetVar()
	{
		$this->object->setVar('somevar', 'somevalue');

		$this->assertEquals(
			'somevalue',
			$this->object->getVar('somevar')
		);
	}

	public function testDelVar()
	{
		$this->assertEquals(
			'value',
			$this->object->getVar('var')
		);

		$this->object->delVar('var');

		$this->assertEquals(
			'',
			$this->object->getVar('var')
		);
	}

	public function testSetQuery()
	{
		$this->object->setQuery('somevar=somevalue');

		$this->assertEquals(
			'somevar=somevalue',
			$this->object->getQuery()
		);

		$this->object->setQuery('somevar=somevalue&amp;test=true');

		$this->assertEquals(
			'somevar=somevalue&test=true',
			$this->object->getQuery()
		);

		$this->object->setQuery(['somevar' => 'somevalue', 'test' => 'true']);

		$this->assertEquals(
			'somevar=somevalue&test=true',
			$this->object->getQuery()
		);
	}

	public function testSetScheme()
	{
		$this->object->setScheme('ftp');

		$this->assertEquals(
			'ftp',
			$this->object->getScheme()
		);
	}

	public function testSetUser()
	{
		$this->object->setUser('root');

		$this->assertEquals(
			'root',
			$this->object->getUser()
		);
	}

	public function testSetPass()
	{
		$this->object->setPass('secret');

		$this->assertEquals(
			'secret',
			$this->object->getPass()
		);
	}

	public function testSetHost()
	{
		$this->object->setHost('www.example.org');

		$this->assertEquals(
			'www.example.org',
			$this->object->getHost()
		);
	}

	public function testSetPort()
	{
		$this->object->setPort('8888');

		$this->assertEquals(
			'8888',
			$this->object->getPort()
		);
	}

	public function testSetPath()
	{
		$this->object->setPath('/this/is/a/path/to/a/file.htm');

		$this->assertEquals(
			'/this/is/a/path/to/a/file.htm',
			$this->object->getPath()
		);
	}

	public function testSetFragment()
	{
		$this->object->setFragment('someFragment');

		$this->assertEquals(
			'someFragment',
			$this->object->getFragment()
		);
	}

	public function testisSsl()
	{
		$this->assertTrue(
			(new Uri('https://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment'))->isSsl()
		);

		$this->assertFalse(
			(new Uri('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment'))->isSsl()
		);
	}
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;
use Joomla\Filesystem\Stream;
use Joomla\Filesystem\Support\StringController;
use Joomla\Test\TestHelper;
use org\bovigo\vfs\vfsStream;

/**
 * Test class for Joomla\Filesystem\Stream.
 */
class StreamTest extends FilesystemTestCase
{
	private const WRITE_PREFIX = 'WRITE_PREFIX/';
	private const READ_PREFIX = 'READ_PREFIX/';

	/**
	 * @var  Stream
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
		parent::setUp();

		$this->object = new Stream(self::WRITE_PREFIX, self::READ_PREFIX);
		vfsStream::setup('root');
	}

	/**
	 * Test counstructor method.
	 */
	public function test__construct()
	{
		$object = new Stream('foo', 'bar');

		$this->assertEquals(
			'foo',
			TestHelper::getValue($object, 'writeprefix')
		);

		$this->assertEquals(
			'bar',
			TestHelper::getValue($object, 'readprefix')
		);

		$this->assertEquals(
			0,
			\count(TestHelper::getValue($object, 'contextOptions'))
		);

		$this->assertEquals(
			null,
			TestHelper::getValue($object, 'context')
		);
	}

	/**
	 * Tests getStream method.
	 */
	public function testGetStream()
	{
		$object = Stream::getStream();

		$this->assertInstanceOf(
			Stream::class,
			$object,
			'getStream must return an instance of Joomla\\Filesystem\\Stream'
		);

		$this->assertEquals(
			dirname(__DIR__) . '/',
			TestHelper::getValue($object, 'writeprefix')
		);

		$this->assertEquals(
			dirname(__DIR__),
			TestHelper::getValue($object, 'readprefix')
		);

		$object = Stream::getStream(false);

		$this->assertInstanceOf(
			Stream::class,
			$object,
			'getStream must return an instance of Joomla\\Filesystem\\Stream'
		);

		$this->assertEquals(
			'',
			TestHelper::getValue($object, 'writeprefix')
		);

		$this->assertEquals(
			'',
			TestHelper::getValue($object, 'readprefix')
		);
	}

	/**
	 * Test open method with no filename.
	 */
	public function testOpenNoFilenameException()
	{
		$this->expectException(\RuntimeException::class);

		$this->object->open('');
	}

	/**
	 * Test open method with invalid filename.
	 */
	public function testOpenInvlaidFilenameException()
	{
		$this->expectException(\RuntimeException::class);

		$this->object->open('foobar');
	}

	/**
	 * Test open method with invalid string name.
	 */
	public function testOpenInvlaidStringnameException()
	{
		$this->expectException(\RuntimeException::class);

		$this->object->open('string://bbarfoo');
	}

	/**
	 * Test open method.
	 */
	public function testOpen()
	{
		// Test simple file open
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->object->open($filename);

		$this->assertEquals(
			$filename,
			TestHelper::getValue($this->object, 'filename')
		);

		$this->assertEquals(
			'r',
			TestHelper::getValue($this->object, 'openmode')
		);

		$this->assertEquals(
			'f',
			TestHelper::getValue($this->object, 'processingmethod')
		);

		$this->assertEquals(
			'resource',
			\gettype(TestHelper::getValue($this->object, 'fh'))
		);

		$this->object->close();
		unlink($filename);

		// Test custom stream open
		$string = "Lorem ipsum dolor sit amet";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->assertEquals(
			$filename,
			TestHelper::getValue($this->object, 'filename')
		);

		$this->object->close();
	}

	/**
	 * Test closing of a stream before opening it.
	 */
	public function testCloseBeforeOpeningException()
	{
		$this->expectException(\RuntimeException::class);

		(new Stream)->close();
	}

	/**
	 * Test eof not found exception.
	 */
	public function testEofNotOpenException()
	{
		$this->expectException(\RuntimeException::class);

		$this->object->eof();
	}

	/**
	 * Test eof method.
	 */
	public function testEof()
	{
		$string = "Lorem ipsum dolor sit amet";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->assertFalse(
			$this->object->eof()
		);

		$this->object->read(\strlen($string));

		$this->assertTrue(
			$this->object->eof()
		);

		$this->object->close();
	}

	/**
	 * Test file size method exception - File not open.
	 */
	public function testFilesizeNotOpen()
	{
		$this->expectException(\RuntimeException::class);

		$this->object->filesize();
	}

	/**
	 * Test filesize method.
	 */
	public function testFilesize()
	{
		$string = "Lorem ipsum dolor sit amet";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->assertEquals(
			\strlen($string),
			$this->object->filesize()
		);

		$this->object->close();

		// Skip remote tests
		if (false)
		{
			$this->object->open('https://www.joomla.org');

			$this->assertTrue(
				is_numeric($this->object->filesize())
			);

			$this->object->close();
		}
	}

	/**
	 * Test gets method's stream not open exception.
	 */
	public function testGetsNotOpen()
	{
		$this->expectException(\RuntimeException::class);

		$this->object->gets();
	}

	/**
	 * Test gets method.
	 */
	public function testGets()
	{
		$string = "Lorem ipsum dolor sit amet.\nFoo bar";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->assertEquals(
			"Lorem ipsum dolor sit amet.\n",
			$this->object->gets()
		);

		$this->assertEquals(
			"Foo",
			$this->object->gets(4)
		);

		$this->object->close();
	}

	/**
	 * Test gets invalid length exception.
	 */
	public function testGetsInvalidLength()
	{
		$this->expectException(\RuntimeException::class);

		$string = "Lorem ipsum dolor sit amet.\nFoo bar";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->object->gets(1);

		$this->object->close();
	}

	/**
	 * Test read method's stream not open exception.
	 */
	public function testReadNotOpen()
	{
		$this->expectException(\RuntimeException::class);

		$this->object->read();
	}

	/**
	 * Test read method.
	 */
	public function testRead()
	{
		$string = "Lorem ipsum dolor sit amet.\nFoo bar";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->assertEquals(
			"L",
			$this->object->read(1)
		);

		$this->assertEquals(
			"orem ipsum dolor sit amet.\nFoo bar",
			$this->object->read()
		);

		$this->object->close();
	}

	/**
	 * Test seek method stream not open exception.
	 */
	public function testSeekNotOpen()
	{
		$this->expectException(\RuntimeException::class);

		$this->object->seek(0);
	}

	/**
	 * Test data for seek test.
	 *
	 * @return  \Generator
	 */
	public function dataSeek(): \Generator
	{
		yield [0, 0, SEEK_SET, 0];
		yield [0, 0, SEEK_CUR, 0];
		yield [0, 0, SEEK_END, 35];
		yield [0, 5, SEEK_SET, 5];
		yield [0, 5, SEEK_CUR, 5];
		yield [0, 5, SEEK_END, 30];
		yield [5, 5, SEEK_SET, 5];
		yield [5, 5, SEEK_CUR, 10];
		yield [5, 5, SEEK_END, 30];
	}

	/**
	 * Test seek method.
	 *
	 * @param   integer  $initial  Intial position of the pointer
	 * @param   integer  $offset   Offset to seek
	 * @param   integer  $whence   Seek type
	 * @param   integer  $expPos   Expected pointer position
	 *
	 * @dataProvider dataSeek
	 */
	public function testSeek($initial, $offset, $whence, $expPos)
	{
		$string = "Lorem ipsum dolor sit amet.\nFoo bar";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);
		$this->object->seek($initial, SEEK_SET);

		$this->assertTrue(
			$this->object->seek($offset, $whence)
		);

		$this->assertEquals(
			$expPos,
			$this->object->tell()
		);

		$this->object->close();
	}

	/**
	 * Test tell method stream not open exception.
	 */
	public function testTellNotOpen()
	{
		$this->expectException(\RuntimeException::class);

		$this->object->tell();
	}

	/**
	 * Test write method stream not open exception.
	 */
	public function testWriteNotOpen()
	{
		$this->expectException(\RuntimeException::class);

		$data = 'foobar';
		$this->object->write($data);
	}

	/**
	 * Test write method with readonly mode excepton.
	 */
	public function testWriteReadonly()
	{
		$this->expectException(\RuntimeException::class);

		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$object = Stream::getStream();

		// Open stream in reading mode.
		$object->open($filename);

		$data = 'foobar';
		$this->assertTrue($object->write($data));

		$object->close();

		unlink($filename);
	}

	/**
	 * Test write method.
	 */
	public function testWrite()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$filename = $path . '/' . $name;

		$object = Stream::getStream();
		$object->open($filename, 'w');

		$data = 'foobar';
		$this->assertTrue($object->write($data));

		$object->close();

		$this->assertStringEqualsFile(
			$filename,
			$data
		);

		unlink($filename);
	}

	/**
	 * Test chmod with no filename exception.
	 */
	public function testChmodNoFilename()
	{
		$this->skipIfUnableToChmod();

		$this->expectException(\RuntimeException::class);

		$this->object->chmod();
	}

	/**
	 * Test chmod method.
	 */
	public function testChmod()
	{
		$this->skipIfUnableToChmod();

		$name = 'tempFile';
		$data = 'Lorem ipsum dolor sit amet';

		if (!File::write($this->testPath . '/' . $name, $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		// The parent test case sets umask(0) therefore we are creating files with 0666 permissions
		$this->assertTrue(
			$this->object->chmod($this->testPath . '/' . $name, 0644)
		);

		// PHP caches permissions lookups, clear it before continuing
		clearstatcache();

		$this->assertSame(
			'rw-r--r--',
			Path::getPermissions($this->testPath . '/' . $name)
		);

		$this->object = Stream::getStream();
		$this->object->open($this->testPath . '/' . $name, 'w');

		$this->assertTrue($this->object->chmod('', 0666));

		clearstatcache();

		$this->assertSame(
			'rw-rw-rw-',
			Path::getPermissions($this->testPath . '/' . $name)
		);

		$this->object->close();
	}

	/**
	 * Test get_meta_data stream not open exception.
	 */
	public function testGet_meta_dataNotOpen()
	{
		$this->expectException(\RuntimeException::class);

		$this->object->get_meta_data();
	}

	/**
	 * Test get_meta_data method.
	 */
	public function testGet_meta_data()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->object->open($filename);
		$metaData = $this->object->get_meta_data();

		$this->assertTrue(
			\is_array($metaData)
		);

		$this->assertEquals(
			$filename,
			$metaData['uri']
		);

		unlink($filename);
	}

	/**
	 * Test buildContext method.
	 */
	public function test_buildContext()
	{
		$contextOptions = [];

		TestHelper::setValue($this->object, 'contextOptions', $contextOptions);
		$this->object->_buildContext();

		$this->assertEquals(
			null,
			TestHelper::getValue($this->object, 'context')
		);

		$contextOptions = [
			'http' => [
				'method' => "GET",
				'header' => "Accept-language: en\r\n" .
					"Cookie: foo=bar\r\n",
			],
		];

		TestHelper::setValue($this->object, 'contextOptions', $contextOptions);
		$this->object->_buildContext();

		$this->assertEquals(
			'resource',
			\gettype(TestHelper::getValue($this->object, 'context'))
		);
	}

	/**
	 * Test setContextOptions method.
	 */
	public function testSetContextOptions()
	{
		$contextOptions = [
			'http' => [
				'method' => "GET",
				'header' => "Accept-language: en\r\n" .
					"Cookie: foo=bar\r\n",
			],
		];

		$this->object->setContextOptions($contextOptions);

		$this->assertEquals(
			$contextOptions,
			TestHelper::getValue($this->object, 'contextOptions')
		);
	}

	/**
	 * Test addContextEntry method.
	 */
	public function testAddContextEntry()
	{
		$this->object->addContextEntry('foo', 'bar', 'barfoo');
		$contextOptions = TestHelper::getValue($this->object, 'contextOptions');

		$this->assertEquals(
			'barfoo',
			$contextOptions['foo']['bar']
		);
	}

	/**
	 * Test deleteContextEntry method.
	 */
	public function testDeleteContextEntry()
	{
		$contextOptions = [
			'foo' => [
				'bar' => 'Bar',
				'rab' => 'Rab',
			],
		];

		TestHelper::setValue($this->object, 'contextOptions', $contextOptions);

		$this->object->deleteContextEntry('foo', 'bar');
		$actual = TestHelper::getValue($this->object, 'contextOptions');

		$this->assertArrayHasKey(
			'foo',
			$actual
		);

		$this->assertArrayHasKey(
			'rab',
			$actual['foo']
		);

		$this->assertArrayNotHasKey(
			'bar',
			$actual['foo']
		);

		$this->object->deleteContextEntry('foo', 'rab');
		$actual = TestHelper::getValue($this->object, 'contextOptions');

		$this->assertArrayNotHasKey(
			'foo',
			$actual
		);
	}

	/**
	 * Test applyContextToStream method.
	 */
	public function testApplyContextToStream()
	{
		$this->assertFalse($this->object->applyContextToStream());

		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->object->open($filename);
		$this->assertTrue($this->object->applyContextToStream());

		unlink($filename);
	}

	/**
	 * Test appendFilter method.
	 */
	public function testAppendFilter()
	{
		$this->expectException(\RuntimeException::class);

		$this->assertFalse($this->object->appendFilter("string.rot13"));

		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->object->open($filename);

		$filters = TestHelper::getValue($this->object, 'filters');

		$this->assertEquals(
			'resource',
			\gettype($this->object->appendFilter("string.rot13"))
		);

		$this->assertEquals(
			\count($filters) + 1,
			\count(TestHelper::getValue($this->object, 'filters'))
		);

		unlink($filename);

		// Tests for invalid filters
		$this->object->appendFilter("foobar");
	}

	/**
	 * Test prependFilter method.
	 */
	public function testPrependFilter()
	{
		$this->expectException(\RuntimeException::class);

		$this->assertFalse($this->object->prependFilter("string.rot13"));

		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->object->open($filename);

		$filters = TestHelper::getValue($this->object, 'filters');

		$this->assertEquals(
			'resource',
			\gettype($this->object->prependFilter("string.rot13"))
		);

		$this->assertEquals(
			\count($filters) + 1,
			\count(TestHelper::getValue($this->object, 'filters'))
		);

		// Tests for invalid filters
		$this->object->prependFilter("foobar");

		unlink($filename);

		$this->object->close();
	}

	/**
	 * Test copy method.
	 */
	public function testCopy()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$copiedFileName = 'copiedTempFile';
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		if (!@file_put_contents($path . '/' . $name, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->assertTrue(
			$this->object->copy($path . '/' . $name, $path . '/' . $copiedFileName, null, false),
			'Line:' . __LINE__ . ' File should copy successfully.'
		);
		$this->assertFileEquals(
			$path . '/' . $name,
			$path . '/' . $copiedFileName
		);
		unlink($path . '/' . $copiedFileName);

		unlink($path . '/' . $name);
	}

	/**
	 * Test move mthod.
	 */
	public function testMove()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$movedFileName = 'copiedTempFile';
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		if (!@file_put_contents($path . '/' . $name, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->assertTrue(
			$this->object->move($path . '/' . $name, $path . '/' . $movedFileName, null, false),
			'Line:' . __LINE__ . ' File should moved successfully.'
		);
		unlink($path . '/' . $movedFileName);

		@unlink($path . '/' . $name);
	}

	/**
	 * Test delete method.
	 */
	public function testDelete()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		if (!@file_put_contents($path . '/' . $name, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->assertFileExists($path . '/' . $name);
		$this->assertTrue(
			$this->object->delete($path . '/' . $name, null, false),
			'Line:' . __LINE__ . ' File should deleted successfully.'
		);

		if (method_exists($this, 'assertFileDoesNotExist'))
		{
			$this->assertFileDoesNotExist($path . '/' . $name);
		}
		else
		{
			$this->assertFileNotExists($path . '/' . $name);
		}

		@unlink($path . '/' . $name);
	}

	/**
	 * Test writeFile method.
	 */
	public function testWriteFile()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';

		$this->assertTrue(
			$this->object->writeFile($path . '/' . $name, $data)
		);

		$this->assertFileExists($path . '/' . $name);
		$this->assertStringEqualsFile(
			$path . '/' . $name,
			$data
		);

		unlink($path . '/' . $name);
	}

	/**
	 * Test write method when appending to a file.
	 */
	public function testwriteFileWithAppend()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$filename = $path . '/' . $name;

		$data = 'Lorem ipsum dolor sit amet';
		$appendData = PHP_EOL . $data;

		$this->assertTrue(
			$this->object->writeFile($path . '/' . $name, $data),
			'The file was not written.'
		);

		$this->assertTrue(
			$this->object->writeFile($path . '/' . $name, $appendData, true),
			'The file was not appended.'
		);

		$this->assertFileExists($path . '/' . $name);
		$this->assertStringEqualsFile(
			$path . '/' . $name,
			$data . $appendData
		);

		unlink($path . '/' . $name);
	}

	/**
	 * Test data for _getFilename test
	 *
	 * @return  \Generator
	 */
	public function data_getFilename(): \Generator
	{
		yield ['', '', 'foobar', 'r', false, false, 'foobar'];
		yield ['', '', 'foobar', 'r', false, true, 'foobar'];
		yield ['', '', 'foobar', 'w', false, false, 'foobar'];
		yield ['', '', 'foobar', 'w', false, true, 'foobar'];
		yield ['one', 'two', 'foobar', 'r', true, false, 'twofoobar'];
		yield ['one', 'two', 'foobar', 'w', true, false, 'onefoobar'];
		yield ['one', 'two', 'foobar', 'r', true, true, 'twofoobar'];
		yield ['one', 'two', 'foobar', 'w', true, true, 'onefoobar'];
		yield ['one', 'two', __DIR__ . '/foobar', 'r', true, false, 'two' . DIRECTORY_SEPARATOR . 'Tests/foobar'];
		yield ['one', 'two', __DIR__ . '/foobar', 'w', true, false, 'one' . DIRECTORY_SEPARATOR . 'Tests/foobar'];
	}

	/**
	 * Test _getFilename method.
	 *
	 * @param   string   $wPrefix     Write prefix
	 * @param   string   $rPrefix     Read prefix
	 * @param   string   $filename    Filename
	 * @param   string   $mode        File open mode
	 * @param   boolean  $use_prefix  Whether to use prefix or not
	 * @param   boolean  $relative    filename is relative or not
	 * @param   string   $expected    Expected path
	 *
	 * @dataProvider data_getFilename
	 */
	public function test_getFilename($wPrefix, $rPrefix, $filename, $mode, $use_prefix, $relative, $expected)
	{
		TestHelper::setValue($this->object, 'writeprefix', $wPrefix);
		TestHelper::setValue($this->object, 'readprefix', $rPrefix);

		$this->assertEquals(
			$expected,
			$this->object->_getFilename($filename, $mode, $use_prefix, $relative)
		);
	}
}

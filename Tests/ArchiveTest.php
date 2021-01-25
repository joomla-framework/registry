<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Tests;

use Joomla\Archive\Archive;
use Joomla\Archive\Exception\UnknownArchiveException;
use Joomla\Archive\Exception\UnsupportedArchiveException;
use Joomla\Archive\Zip as ArchiveZip;

/**
 * Test class for Joomla\Archive\Archive.
 */
class ArchiveTest extends ArchiveTestCase
{
	/**
	 * Object under test
	 *
	 * @var  Archive
	 */
	protected $fixture;

	/**
	 * Sets up the fixture.
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->fixture = new Archive;
	}

	/**
	 * Data provider for retrieving adapters.
	 *
	 * @return  \Generator
	 */
	public function dataAdapters(): \Generator
	{
		// Adapter Type, Expected Exception
		yield 'Zip Adapter' => ['Zip', false];
		yield 'Tar Adapter' => ['Tar', false];
		yield 'Gzip Adapter' => ['Gzip', false];
		yield 'Bzip2 Adapter' => ['Bzip2', false];
		yield 'Unknown Adapter' => ['Unknown', true];
	}

	/**
	 * Data provider for extracting archives.
	 *
	 * @return  \Generator
	 */
	public function dataExtract(): \Generator
	{
		// Filename, Adapter Type, Extracted Filename, Output is a File
		yield 'Zip adapter with capitalised file extension' => ['Caps-Logo.ZIP', 'Zip', 'logo-zip.png'];
		yield 'Zip adapter' => ['logo.zip', 'Zip', 'logo-zip.png'];
		yield 'Tar adapter' => ['logo.tar', 'Zip', 'logo-tar.png'];
		yield 'Gzip adapter with .gz file type' => ['logo.png.gz', 'Gzip', 'logo.png'];
		yield 'Bzip2 adapter with .bz2 file type' => ['logo.png.bz2', 'Bzip2', 'logo.png'];
		yield 'Gzip adapter with .tar.gz file type' => ['logo.tar.gz', 'Gzip', 'logo-tar-gz.png'];
		yield 'Bzip2 adapter with .tar.bz2 file type' => ['logo.tar.bz2', 'Bzip2', 'logo-tar-bz2.png'];
	}

	/**
	 * @testdox  The Archive object is instantiated correctly
	 *
	 * @covers   Joomla\Archive\Archive
	 */
	public function test__construct()
	{
		$options = ['tmp_path' => __DIR__];

		$fixture = new Archive($options);

		$this->assertSame($options, $fixture->options);
	}

	/**
	 * @testdox  Archives can be extracted
	 *
	 * @param   string   $filename           Name of the file to extract
	 * @param   string   $adapterType        Type of adaptar that will be used
	 * @param   string   $extractedFilename  Name of the file to extracted file
	 *
	 * @covers        Joomla\Archive\Archive
	 * @uses          Joomla\Archive\Bzip2
	 * @uses          Joomla\Archive\Gzip
	 * @uses          Joomla\Archive\Tar
	 * @uses          Joomla\Archive\Zip
	 * @dataProvider  dataExtract
	 */
	public function testExtract($filename, $adapterType, $extractedFilename)
	{
		if (!is_writable($this->outputPath) || !is_writable($this->fixture->options['tmp_path']))
		{
			$this->markTestSkipped('Folder not writable.');
		}

		$adapter = "Joomla\\Archive\\$adapterType";

		if (!$adapter::isSupported())
		{
			$this->markTestSkipped($adapterType . ' files can not be extracted.');
		}

		$this->assertTrue(
			$this->fixture->extract($this->inputPath . "/$filename", $this->outputPath)
		);

		$this->assertFileExists($this->outputPath . "/$extractedFilename");

		@unlink($this->outputPath . "/$extractedFilename");
	}

	/**
	 * @testdox  Extracting an unknown archive type throws an Exception
	 *
	 * @covers   Joomla\Archive\Archive
	 */
	public function testExtractUnknown()
	{
		$this->expectException(UnknownArchiveException::class);

		$this->fixture->extract(
			$this->inputPath . '/logo.dat',
			$this->outputPath
		);
	}

	/**
	 * @testdox  Adapters can be retrieved
	 *
	 * @param   string   $adapterType        Type of adapter to load
	 * @param   boolean  $expectedException  Flag if an Exception is expected
	 *
	 * @covers        Joomla\Archive\Archive
	 * @uses          Joomla\Archive\Bzip2
	 * @uses          Joomla\Archive\Gzip
	 * @uses          Joomla\Archive\Tar
	 * @uses          Joomla\Archive\Zip
	 * @dataProvider  dataAdapters
	 */
	public function testGetAdapter($adapterType, $expectedException)
	{
		if ($expectedException)
		{
			$this->expectException(UnsupportedArchiveException::class);
		}

		$adapter = $this->fixture->getAdapter($adapterType);

		$this->assertInstanceOf('Joomla\\Archive\\' . $adapterType, $adapter);
	}

	/**
	 * @testdox  Adapters can be set to the Archive
	 *
	 * @covers   Joomla\Archive\Archive
	 * @uses     Joomla\Archive\Zip
	 */
	public function testSetAdapter()
	{
		$this->assertSame(
			$this->fixture,
			$this->fixture->setAdapter('zip', new ArchiveZip),
			'The setAdapter method should return the current object.'
		);
	}

	/**
	 * @testdox  Setting an unknown adapter throws an Exception
	 *
	 * @covers   Joomla\Archive\Archive
	 */
	public function testSetAdapterUnknownException()
	{
		$this->expectException(UnsupportedArchiveException::class);

		$this->fixture->setAdapter('unknown', 'unknown-class');
	}
}

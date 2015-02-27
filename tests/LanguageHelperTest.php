<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\LanguageHelper;

/**
 * Test class for Joomla\Language\LanguageHelper.
 */
class LanguageHelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test language object
	 *
	 * @var  LanguageHelper
	 */
	protected $object;

	/**
	 * Path to language folder used for testing
	 *
	 * @var  string
	 */
	private $testPath;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->testPath = __DIR__ . '/data';
		$this->object   = new LanguageHelper;
	}

	/**
	 * @testdox  Verify that LanguageHelper::exists() locates the language directory
	 *
	 * @covers   Joomla\Language\LanguageHelper::exists
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyExistsLocatesTheLanguageDirectory()
	{
		$this->assertTrue($this->object->exists('en-GB', $this->testPath));
	}

	/**
	 * @testdox  Verify that LanguageHelper::getMetadata() returns the language metadata
	 *
	 * @covers   Joomla\Language\LanguageHelper::getMetadata
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyGetMetadataReturnsTheLanguageMetadata()
	{
		$this->assertInternalType('array', $this->object->getMetadata('en-GB', $this->testPath));
	}

	/**
	 * @testdox  Verify that LanguageHelper::getMetadata() returns null if metadata does not exist
	 *
	 * @covers   Joomla\Language\LanguageHelper::getMetadata
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyGetMetadataReturnsNullIfMetadataDoesNotExist()
	{
		$this->assertNull($this->object->getMetadata('es-ES', $this->testPath));
	}

	/**
	 * @testdox  Verify that Language::getKnownLanguages() returns an array of known languages
	 *
	 * @covers   Joomla\Language\LanguageHelper::getKnownLanguages
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyGetKnownLanguagesReturnsAnArrayOfKnownLanguages()
	{
		$this->assertInternalType('array', $this->object->getKnownLanguages($this->testPath));
	}

	/**
	 * @testdox  Verify that Language::getLanguagePath() returns the correct language path
	 *
	 * @covers   Joomla\Language\LanguageHelper::getLanguagePath
	 */
	public function testVerifyGetLanguagePathReturnsTheCorrectLanguagePath()
	{
		$this->assertSame($this->testPath . '/language', $this->object->getLanguagePath($this->testPath));
	}

	/**
	 * @testdox  Verify that Language::parseLanguageFiles() returns an array
	 *
	 * @covers   Joomla\Language\LanguageHelper::parseLanguageFiles
	 * @uses     Joomla\Language\LanguageHelper
	 * @deprecated
	 */
	public function testVerifyParseLanguageFilesReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->parseLanguageFiles($this->testPath));
	}

	/**
	 * @testdox  Verify that Language::parseXMLLanguageFile() returns an array
	 *
	 * @covers   Joomla\Language\LanguageHelper::parseXMLLanguageFile
	 */
	public function testVerifyParseXMLLanguageFileReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->parseXMLLanguageFile($this->testPath . '/language/en-GB/en-GB.xml'));
	}

	/**
	 * @testdox  Verify that Language::parseXMLLanguageFile() returns null if the top XML tag is not metafile
	 *
	 * @covers   Joomla\Language\LanguageHelper::parseXMLLanguageFile
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyParseXMLLanguageFileReturnsNullIfTheTopXMLTagIsNotMetafile()
	{
		$this->assertNull($this->object->parseXMLLanguageFile($this->testPath . '/language/xx-XX/xx-XX.xml'));
	}

	/**
	 * @testdox  Verify that Language::parseXMLLanguageFile() throws an exception if the file is not found
	 *
	 * @covers             Joomla\Language\LanguageHelper::parseXMLLanguageFile
	 * @uses               Joomla\Language\LanguageHelper
	 * @expectedException  \RuntimeException
	 */
	public function testVerifyParseXMLLanguageFileThrowsAnExceptionIfTheFileIsNotFound()
	{
		$this->object->parseXMLLanguageFile($this->testPath . '/xx-XX/xx-XX.xml');
	}
}

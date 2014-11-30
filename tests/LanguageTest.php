<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\Language;
use Joomla\Filesystem\Folder;
use Joomla\Test\TestHelper;

/**
 * Test class for Joomla\Language\Language.
 */
class LanguageTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test language object
	 *
	 * @var  Language
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
		$this->object   = new Language($this->testPath);
	}

	/**
	 * @testdox  Verify that Language is instantiated correctly
	 *
	 * @covers   Joomla\Language\Language::__construct
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatLanguageIsInstantiatedCorrectly()
	{
		$this->assertInstanceOf('Joomla\\Language\\Language', new Language($this->testPath));
	}

	/**
	 * @testdox  Verify that getInstance() returns a Language object
	 *
	 * @covers   Joomla\Language\Language::getInstance
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyGetInstanceReturnsALanguageObject()
	{
		$this->assertInstanceOf('\\Joomla\\Language\\Language', Language::getInstance($this->testPath));
	}

	/**
	 * @testdox  Verify that Language::_() proxies to Language::translate()
	 *
	 * @covers   Joomla\Language\Language::_
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testUnderscoreMethodProxiesToTranslate()
	{
		$this->assertEmpty($this->object->_(''));
	}

	/**
	 * @testdox  Verify that Language::translate() returns an empty string when one is input
	 *
	 * @covers   Joomla\Language\Language::translate
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testTranslateReturnsEmptyStringWhenGivenAnEmptyString()
	{
		$this->assertEmpty($this->object->translate(''));
	}

	/**
	 * @testdox  Verify that Language::transliterate() returns a string
	 *
	 * @covers   Joomla\Language\Language::transliterate
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 * @uses     Joomla\Language\Transliterate
	 * @uses     Joomla\String\String
	 */
	public function testTransliterateReturnsAString()
	{
		$this->assertSame('Así', $this->object->transliterate('Así'));
	}

	/**
	 * @testdox  Verify that Language::getTransliterator() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getTransliterator
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testGetTransliteratorDefaultReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getTransliterator());
	}

	/**
	 * @testdox  Verify that Language::setTransliterator() default returns the previous callback
	 *
	 * @covers   Joomla\Language\Language::setTransliterator
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testSetTransliteratorDefaultReturnsThePreviousCallback()
	{
		$this->assertInternalType('array', $this->object->setTransliterator('print'));
	}

	/**
	 * @testdox  Verify that Language::getPluralSuffixes() returns an array
	 *
	 * @covers   Joomla\Language\Language::getPluralSuffixes
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testGetPluralSuffixesReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getPluralSuffixes(1));
	}

	/**
	 * @testdox  Verify that Language::getPluralSuffixesCallback() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getPluralSuffixesCallback
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testGetPluralSuffixesCallbackDefaultReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getPluralSuffixesCallback());
	}

	/**
	 * @testdox  Verify that Language::setPluralSuffixesCallback() default returns the previous callback
	 *
	 * @covers   Joomla\Language\Language::setPluralSuffixesCallback
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testSetPluralSuffixesCallbackDefaultReturnsThePreviousCallback()
	{
		$this->assertInternalType('array', $this->object->setPluralSuffixesCallback('print'));
	}

	/**
	 * @testdox  Verify that Language::getIgnoredSearchWords() returns an array
	 *
	 * @covers   Joomla\Language\Language::getIgnoredSearchWords
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testGetIgnoredSearchWordsReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getIgnoredSearchWords());
	}

	/**
	 * @testdox  Verify that Language::getIgnoredSearchWordsCallback() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getIgnoredSearchWordsCallback
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testGetIgnoredSearchWordsCallbackDefaultReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getIgnoredSearchWordsCallback());
	}

	/**
	 * @testdox  Verify that Language::setIgnoredSearchWordsCallback() default returns the previous callback
	 *
	 * @covers   Joomla\Language\Language::setIgnoredSearchWordsCallback
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testSetIgnoredSearchWordsCallbackDefaultReturnsThePreviousCallback()
	{
		$this->assertInternalType('array', $this->object->setIgnoredSearchWordsCallback('print'));
	}

	/**
	 * @testdox  Verify that Language::getLowerLimitSearchWord() returns an integer
	 *
	 * @covers   Joomla\Language\Language::getLowerLimitSearchWord
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testGetLowerLimitSearchWordReturnsAnInteger()
	{
		$this->assertInternalType('integer', $this->object->getLowerLimitSearchWord());
	}

	/**
	 * @testdox  Verify that Language::getLowerLimitSearchWordCallback() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getLowerLimitSearchWordCallback
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testGetLowerLimitSearchWordCallbackDefaultReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getLowerLimitSearchWordCallback());
	}

	/**
	 * @testdox  Verify that Language::setLowerLimitSearchWordCallback() default returns the previous callback
	 *
	 * @covers   Joomla\Language\Language::setLowerLimitSearchWordCallback
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testSetLowerLimitSearchWordCallbackDefaultReturnsThePreviousCallback()
	{
		$this->assertInternalType('array', $this->object->setLowerLimitSearchWordCallback('print'));
	}

	/**
	 * @testdox  Verify that Language::getUpperLimitSearchWord() returns an integer
	 *
	 * @covers   Joomla\Language\Language::getUpperLimitSearchWord
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testGetUpperLimitSearchWordReturnsAnInteger()
	{
		$this->assertInternalType('integer', $this->object->getUpperLimitSearchWord());
	}

	/**
	 * @testdox  Verify that Language::getUpperLimitSearchWordCallback() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getUpperLimitSearchWordCallback
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testGetUpperLimitSearchWordCallbackDefaultReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getUpperLimitSearchWordCallback());
	}

	/**
	 * @testdox  Verify that Language::setUpperLimitSearchWordCallback() default returns the previous callback
	 *
	 * @covers   Joomla\Language\Language::setUpperLimitSearchWordCallback
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testSetUpperLimitSearchWordCallbackDefaultReturnsThePreviousCallback()
	{
		$this->assertInternalType('array', $this->object->setUpperLimitSearchWordCallback('print'));
	}

	/**
	 * @testdox  Verify that Language::getSearchDisplayedCharactersNumber() returns an integer
	 *
	 * @covers   Joomla\Language\Language::getSearchDisplayedCharactersNumber
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testGetSearchDisplayedCharactersNumberReturnsAnInteger()
	{
		$this->assertInternalType('integer', $this->object->getSearchDisplayedCharactersNumber());
	}

	/**
	 * @testdox  Verify that Language::getSearchDisplayedCharactersNumberCallback() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getSearchDisplayedCharactersNumberCallback
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testGetSearchDisplayedCharactersNumberCallbackDefaultReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getSearchDisplayedCharactersNumberCallback());
	}

	/**
	 * @testdox  Verify that Language::setSearchDisplayedCharactersNumberCallback() default returns the previous callback
	 *
	 * @covers   Joomla\Language\Language::setSearchDisplayedCharactersNumberCallback
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testSetSearchDisplayedCharactersNumberCallbackDefaultReturnsThePreviousCallback()
	{
		$this->assertInternalType('array', $this->object->setSearchDisplayedCharactersNumberCallback('print'));
	}

	/**
	 * @testdox  Verify that Language::exists() proxies to LanguageHelper::exists()
	 *
	 * @covers   Joomla\Language\Language::exists
	 * @covers   Joomla\Language\LanguageHelper::exists
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 * @deprecated
	 */
	public function testVerifyExistsProxiesToLanguageHelper()
	{
		$this->assertTrue($this->object->exists('en-GB', $this->testPath));
	}

	/**
	 * @testdox  Verify that Language::load() successfully loads the main language file
	 *
	 * @covers   Joomla\Language\Language::load
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyLoadSuccessfullyLoadsTheMainLanguageFile()
	{
		$this->assertTrue($this->object->load());
	}

	/**
	 * @testdox  Verify that Language::load() successfully loads a language file
	 *
	 * @covers   Joomla\Language\Language::loadLanguage
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyLoadLanguageSuccessfullyLoadsALanguageFile()
	{
		$this->assertTrue(TestHelper::invoke($this->object, 'loadLanguage', $this->testPath . '/good.ini'));
	}

	/**
	 * @testdox  Verify that Language::parse() successfully parses a language file
	 *
	 * @covers   Joomla\Language\Language::parse
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyParseSuccessfullyParsesALanguageFile()
	{
		$this->assertNotEmpty(TestHelper::invoke($this->object, 'parse', $this->testPath . '/good.ini'));
	}

	/**
	 * @testdox  Verify that Language::get() returns the correct metadata
	 *
	 * @covers   Joomla\Language\Language::get
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatGetReturnsTheCorrectMetadata()
	{
		$this->assertEquals('en-GB', $this->object->get('tag'));
	}

	/**
	 * @testdox  Verify that Language::getCallerInfo() returns an array
	 *
	 * @covers   Joomla\Language\Language::getCallerInfo
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyGetCallerInfoReturnsAnArray()
	{
		$this->assertInternalType('array', TestHelper::invoke($this->object, 'getCallerInfo'));
	}

	/**
	 * @testdox  Verify that Language::getName() returns the correct metadata
	 *
	 * @covers   Joomla\Language\Language::getName
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatGetNameReturnsTheCorrectMetadata()
	{
		$this->assertSame('English (United Kingdom)', $this->object->getName());
	}

	/**
	 * @testdox  Verify that Language::getPaths() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getPaths
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatGetPathsDefaultReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getPaths());
	}

	/**
	 * @testdox  Verify that Language::getErrorFiles() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getErrorFiles
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatGetErrorFilesDefaultReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getErrorFiles());
	}

	/**
	 * @testdox  Verify that Language::getTag() returns the correct metadata
	 *
	 * @covers   Joomla\Language\Language::getTag
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatGetTagReturnsTheCorrectMetadata()
	{
		$this->assertSame('en-GB', $this->object->getTag());
	}

	/**
	 * @testdox  Verify that Language::isRTL() default returns false
	 *
	 * @covers   Joomla\Language\Language::isRTL
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatIsRTLDefaultReturnsFalse()
	{
		$this->assertFalse($this->object->isRTL());
	}

	/**
	 * @testdox  Verify that Language::setDebug() returns the previous debug state
	 *
	 * @covers   Joomla\Language\Language::setDebug
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatSetDebugReturnsThePreviousDebugState()
	{
		$this->assertFalse($this->object->setDebug(true));
	}

	/**
	 * @testdox  Verify that Language::getDebug() default returns false
	 *
	 * @covers   Joomla\Language\Language::getDebug
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatGetDebugDefaultReturnsFalse()
	{
		$this->assertFalse($this->object->getDebug());
	}

	/**
	 * @testdox  Verify that Language::setDefault() returns the previous default language
	 *
	 * @covers   Joomla\Language\Language::setDefault
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatSetDefaultReturnsThePreviousDefaultLanguage()
	{
		$this->assertSame('en-GB', $this->object->setDefault('de-DE'));
	}

	/**
	 * @testdox  Verify that Language::getDefault() default returns 'en-GB'
	 *
	 * @covers   Joomla\Language\Language::getDefault
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyTheDefaultReturnForGetDefault()
	{
		$this->assertSame('en-GB', $this->object->getDefault());
	}

	/**
	 * @testdox  Verify that Language::getOrphans() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getOrphans
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatGetOrphansDefaultReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getOrphans());
	}

	/**
	 * @testdox  Verify that Language::getUsed() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getUsed
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatGetUsedDefaultReturnsAnArray()
	{
		$this->assertInternalType('array', $this->object->getUsed());
	}

	/**
	 * @testdox  Verify that Language::hasKey() returns false for a non-existing language key
	 *
	 * @covers   Joomla\Language\Language::hasKey
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatHasKeyReturnsFalseForANonExistingLanguageKey()
	{
		$this->assertFalse($this->object->hasKey('com_admin.key'));
	}

	/**
	 * @testdox  Verify that Language::getMetadata() proxies to LanguageHelper::getMetadata()
	 *
	 * @covers   Joomla\Language\Language::getMetadata
	 * @covers   Joomla\Language\LanguageHelper::getMetadata
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 * @deprecated
	 */
	public function testVerifyGetMetadataProxiesToLanguageHelper()
	{
		$this->assertInternalType('array', $this->object->getMetadata('en-GB', $this->testPath));
	}

	/**
	 * @testdox  Verify that Language::getKnownLanguages() proxies to LanguageHelper::getKnownLanguages()
	 *
	 * @covers   Joomla\Language\Language::getKnownLanguages
	 * @covers   Joomla\Language\LanguageHelper::getKnownLanguages
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 * @deprecated
	 */
	public function testVerifyGetKnownLanguagesProxiesToLanguageHelper()
	{
		$this->assertArrayHasKey('en-GB', $this->object->getKnownLanguages($this->testPath));
	}

	/**
	 * @testdox  Verify that Language::getLanguagePath() proxies to LanguageHelper::getLanguagePath()
	 *
	 * @covers   Joomla\Language\Language::getLanguagePath
	 * @covers   Joomla\Language\LanguageHelper::getLanguagePath
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 * @deprecated
	 */
	public function testVerifyGetLanguagePathProxiesToLanguageHelper()
	{
		$this->assertSame($this->testPath . '/language', $this->object->getLanguagePath($this->testPath));
	}

	/**
	 * @testdox  Verify that Language::setLanguage() returns the previous default language
	 *
	 * @covers   Joomla\Language\Language::setLanguage
	 * @uses     Joomla\Language\LanguageHelper::getMetadata
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyThatSetLanguageReturnsThePreviousLanguage()
	{
		$this->assertSame('en-GB', $this->object->setLanguage('de-DE'));
	}

	/**
	 * @testdox  Verify that Language::getLanguage() default returns 'en-GB'
	 *
	 * @covers   Joomla\Language\Language::getLanguage
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyTheDefaultReturnForGetLanguage()
	{
		$this->assertSame('en-GB', $this->object->getLanguage());
	}

	/**
	 * @testdox  Verify that Language::getLocale() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getLocale
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyTheDefaultReturnForGetLocale()
	{
		$this->assertInternalType('array', $this->object->getLocale());
	}

	/**
	 * @testdox  Verify that Language::getFirstDay() default returns an array
	 *
	 * @covers   Joomla\Language\Language::getFirstDay
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyTheDefaultReturnForGetFirstDay()
	{
		$this->assertSame(0, $this->object->getFirstDay());
	}

	/**
	 * @testdox  Verify that Language::parseLanguageFiles() proxies to LanguageHelper::parseLanguageFiles()
	 *
	 * @covers   Joomla\Language\Language::parseLanguageFiles
	 * @covers   Joomla\Language\LanguageHelper::parseLanguageFiles
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 * @deprecated
	 */
	public function testVerifyParseLanguageFilesProxiesToLanguageHelper()
	{
		$this->assertInternalType('array', $this->object->parseLanguageFiles($this->testPath));
	}

	/**
	 * @testdox  Verify that Language::parseXMLLanguageFile() proxies to LanguageHelper::parseXMLLanguageFile()
	 *
	 * @covers   Joomla\Language\Language::parseXMLLanguageFile
	 * @covers   Joomla\Language\LanguageHelper::parseXMLLanguageFile
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 * @deprecated
	 */
	public function testVerifyParseXMLLanguageFileProxiesToLanguageHelper()
	{
		$this->assertInternalType('array', $this->object->parseXMLLanguageFile($this->testPath . '/language/en-GB/en-GB.xml'));
	}

	/**
	 * Tests the _ method
	 *
	 * @covers  Joomla\Language\Language::_
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function test_()
	{
		$string1 = 'delete';
		$string2 = "delete's";

		$this->assertEquals(
			'',
			$this->object->_('', false),
			'Line: ' . __LINE__ . ' Empty string should return as it is when javascript safe is false '
		);

		$this->assertEquals(
			'',
			$this->object->_('', true),
			'Line: ' . __LINE__ . ' Empty string should return as it is when javascript safe is true '
		);

		$this->assertEquals(
			'delete',
			$this->object->_($string1, false),
			'Line: ' . __LINE__ . ' Exact case should match when javascript safe is false '
		);

		$this->assertNotEquals(
			'Delete',
			$this->object->_($string1, false),
			'Line: ' . __LINE__ . ' Should be case sensitive when javascript safe is false'
		);

		$this->assertEquals(
			'delete',
			$this->object->_($string1, true),
			'Line: ' . __LINE__ . ' Exact case match should work when javascript safe is true'
		);

		$this->assertNotEquals(
			'Delete',
			$this->object->_($string1, true),
			'Line: ' . __LINE__ . ' Should be case sensitive when javascript safe is true'
		);

		$this->assertEquals(
			'delete\'s',
			$this->object->_($string2, false),
			'Line: ' . __LINE__ . ' Exact case should match when javascript safe is false '
		);

		$this->assertNotEquals(
			'Delete\'s',
			$this->object->_($string2, false),
			'Line: ' . __LINE__ . ' Should be case sensitive when javascript safe is false'
		);

		$this->assertEquals(
			"delete\'s",
			$this->object->_($string2, true),
			'Line: ' . __LINE__ . ' Exact case should match when javascript safe is true, also it calls addslashes (\' => \\\') '
		);

		$this->assertNotEquals(
			"Delete\'s",
			$this->object->_($string2, true),
			'Line: ' . __LINE__ . ' Should be case sensitive when javascript safe is true,, also it calls addslashes (\' => \\\') '
		);
	}

	/**
	 * Tests the _ method with strings loaded and debug enabled
	 *
	 * @covers  Joomla\Language\Language::_
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function test_WithLoadedStringsAndDebug()
	{
		// Loading some strings.
		TestHelper::setValue($this->object, 'strings', array('DEL' => 'Delete'));

		$this->assertEquals(
			"Delete",
			$this->object->_('del', true)
		);

		$this->assertEquals(
			"Delete",
			$this->object->_('DEL', true)
		);

		// Debug true tests
		TestHelper::setValue($this->object, 'debug', true);

		$this->assertArrayNotHasKey(
			'DEL',
			TestHelper::getValue($this->object, 'used')
		);
		$this->assertEquals(
			"**Delete**",
			$this->object->_('del', true)
		);
		$this->assertArrayHasKey(
			'DEL',
			TestHelper::getValue($this->object, 'used')
		);

		$this->assertArrayNotHasKey(
			'DELET\\ED',
			TestHelper::getValue($this->object, 'orphans')
		);
		$this->assertEquals(
			"??Delet\\\\ed??",
			$this->object->_('Delet\\ed', true)
		);
		$this->assertArrayHasKey(
			'DELET\\ED',
			TestHelper::getValue($this->object, 'orphans')
		);
	}
}

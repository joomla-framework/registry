<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\Language;
use Joomla\Language\LanguageFactory;
use Joomla\Language\Localise\En_GBLocalise;
use Joomla\Language\Stemmer\Porteren;
use Joomla\Language\Text;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Language\Language.
 */
class LanguageFactoryTest extends TestCase
{
	/**
	 * Test language object
	 *
	 * @var  LanguageFactory
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
	protected function setUp(): void
	{
		parent::setUp();

		$this->testPath = __DIR__ . '/data';
		$this->object   = new LanguageFactory;
	}

	/**
	 * @testdox  Verify the default return of getDefaultLanguage()
	 *
	 * @covers   Joomla\Language\LanguageFactory::getDefaultLanguage
	 */
	public function testTheDefaultReturnOfGetDefaultLanguage()
	{
		$this->assertSame('en-GB', $this->object->getDefaultLanguage());
	}

	/**
	 * @testdox  Verify the default return of getLanguageDirectory()
	 *
	 * @covers   Joomla\Language\LanguageFactory::getLanguageDirectory
	 */
	public function testTheDefaultReturnOfGetLanguageDirectory()
	{
		$this->assertEmpty($this->object->getLanguageDirectory());
	}

	/**
	 * @testdox  Verify that getLocalise() returns the default localise class when none exists
	 *
	 * @covers   Joomla\Language\LanguageFactory::getLocalise
	 */
	public function testVerifyGetLocaliseReturnsDefaultLocaliseWhenNoneExists()
	{
		$this->assertInstanceOf(En_GBLocalise::class, $this->object->getLocalise('fr-FR', $this->testPath));
	}

	/**
	 * @testdox  Verify that getLocalise() returns the correct localise class when it exists
	 *
	 * @covers   Joomla\Language\LanguageFactory::getLocalise
	 */
	public function testVerifyGetLocaliseReturnedWhenExists()
	{
		// Class exists check in PHPUnit happens before we import the file in our method
		require_once $this->testPath . '/language/xx-XX/xx-XX.localise.php';

		$this->assertInstanceOf('\\Xx_XXLocalise', $this->object->getLocalise('xx-XX', $this->testPath));
	}

	/**
	 * @testdox  Verify that getLocalise() validates the cache when a localise object exists
	 *
	 * @covers   Joomla\Language\LanguageFactory::getLocalise
	 */
	public function testVerifyGetLocaliseValidatesTheCacheWhenALocaliseObjectExists()
	{
		// Class exists check in PHPUnit happens before we import the file in our method
		require_once $this->testPath . '/language/xx-XX/xx-XX.localise.php';

		// Call the method once to fill the cache
		$this->object->getLocalise('xx-XX', $this->testPath);

		$this->assertInstanceOf('\\Xx_XXLocalise', $this->object->getLocalise('xx-XX', $this->testPath));
	}

	/**
	 * @testdox  Verify that getLanguage() returns a Language object
	 *
	 * @covers   Joomla\Language\LanguageFactory::getLanguage
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyGetLanguageReturnsALanguageObject()
	{
		$this->assertInstanceOf(Language::class, $this->object->getLanguage(null, $this->testPath));
	}

	/**
	 * @testdox  Verify that getLanguage() throws an \InvalidArgumentException when no path is given
	 *
	 * @covers   Joomla\Language\LanguageFactory::getLanguage
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 */
	public function testVerifyGetLanguageThrowsAnExceptionWhenNoPathIsGiven()
	{
		$this->expectException(\InvalidArgumentException::class);

		$this->object->getLanguage('es-ES');
	}

	/**
	 * @testdox  Verify that getText() returns a Text object
	 *
	 * @covers   Joomla\Language\LanguageFactory::getText
	 * @uses     Joomla\Language\Language
	 * @uses     Joomla\Language\LanguageHelper
	 * @uses     Joomla\Language\Text
	 */
	public function testVerifyThatGetTextReturnsATextObject()
	{
		$language = $this->object->getLanguage(null, $this->testPath);
		$this->assertInstanceOf(Text::class, $this->object->getText($language));
	}

	/**
	 * @testdox  Verify getInstance() returns an instance of the correct object
	 *
	 * @covers   Joomla\Language\LanguageFactory::getStemmer
	 */
	public function testGetStemmerReturnsAnInstanceOfTheCorrectObject()
	{
		$this->assertInstanceOf(Porteren::class, $this->object->getStemmer('porteren'));
	}

	/**
	 * @testdox  Verify getInstance() returns an instance of the correct object
	 *
	 * @covers   Joomla\Language\LanguageFactory::getStemmer
	 */
	public function testGetStemmerThrowsAnExceptionIfTheObjectDoesNotExist()
	{
		$this->expectException(\RuntimeException::class);

		$this->object->getStemmer('unexisting');
	}

	/**
	 * @testdox  Verify setDefaultLanguage() returns the current object
	 *
	 * @covers   Joomla\Language\LanguageFactory::setDefaultLanguage
	 */
	public function testSetDefaultLanguageReturnsTheCurrentObject()
	{
		$this->assertSame($this->object, $this->object->setDefaultLanguage('en-US'));
	}

	/**
	 * @testdox  Verify setLanguageDirectory() returns the current object
	 *
	 * @covers   Joomla\Language\LanguageFactory::setLanguageDirectory
	 */
	public function testSetLanguageDirectoryReturnsTheCurrentObject()
	{
		$this->assertSame($this->object, $this->object->setLanguageDirectory($this->testPath));
	}

	/**
	 * @testdox  Verify setLanguageDirectory() throws an exception when a path does not exist
	 *
	 * @covers   Joomla\Language\LanguageFactory::setLanguageDirectory
	 */
	public function testSetLanguageDirectoryThrowsAnExceptionWhenAPathDoesNotExist()
	{
		$this->expectException(\InvalidArgumentException::class);

		$this->object->setLanguageDirectory(__DIR__ . '/negative-tester');
	}
}

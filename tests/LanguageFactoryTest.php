<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\LanguageFactory;
use Joomla\Test\TestHelper;

/**
 * Test class for Joomla\Language\Language.
 */
class LanguageFactoryTest extends \PHPUnit_Framework_TestCase
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
	protected function setUp()
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
		$this->assertNull($this->object->getLanguageDirectory());
	}

	/**
	 * @testdox  Verify that getLocalise() returns the default localise class when none exists
	 *
	 * @covers   Joomla\Language\LanguageFactory::getLocalise
	 */
	public function testVerifyGetLocaliseReturnsDefaultLocaliseWhenNoneExists()
	{
		$this->assertInstanceOf('\\Joomla\\Language\\Localise\\En_GBLocalise', $this->object->getLocalise('fr-FR', $this->testPath));
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
	 * @testdox  Verify that getLocalise() throws an exception if the localise file doesn't follow the interface
	 *
	 * @covers             Joomla\Language\LanguageFactory::getLocalise
	 * @expectedException  \RuntimeException
	 */
	public function testVerifyGetLocaliseThrowsAnExceptionIfTheFileIsNotFound()
	{
		require_once $this->testPath . '/language/yy-YY/yy-YY.localise.php';

		$this->object->getLocalise('yy-YY', $this->testPath);
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
		$this->assertInstanceOf('\\Joomla\\Language\\Language', $this->object->getLanguage(null, $this->testPath));
	}

	/**
	 * @testdox  Verify that getLanguage() throws an \InvalidArgumentException when no path is given
	 *
	 * @covers             Joomla\Language\LanguageFactory::getLanguage
	 * @uses               Joomla\Language\Language
	 * @uses               Joomla\Language\LanguageHelper
	 * @expectedException  \InvalidArgumentException
	 */
	public function testVerifyGetLanguageThrowsAnExceptionWhenNoPathIsGiven()
	{
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
		$this->assertInstanceOf('\\Joomla\\Language\\Text', $this->object->getText($language));
	}

	/**
	 * @testdox  Verify getInstance() returns an instance of the correct object
	 *
	 * @covers   Joomla\Language\LanguageFactory::getStemmer
	 */
	public function testGetStemmerReturnsAnInstanceOfTheCorrectObject()
	{
		$this->assertInstanceOf('\\Joomla\\Language\\Stemmer\\Porteren', $this->object->getStemmer('porteren'));
	}

	/**
	 * @testdox  Verify getInstance() returns the cached object
	 *
	 * @covers   Joomla\Language\LanguageFactory::getStemmer
	 */
	public function testGetStemmerReturnsTheCachedObject()
	{
		$firstInstance  = $this->object->getStemmer('porteren');
		$cachedInstance = $this->object->getStemmer('porteren');

		$this->assertSame($firstInstance, $cachedInstance);
	}

	/**
	 * @testdox  Verify getInstance() returns an instance of the correct object
	 *
	 * @covers             Joomla\Language\LanguageFactory::getStemmer
	 * @expectedException  \RuntimeException
	 */
	public function testGetStemmerThrowsAnExceptionIfTheObjectDoesNotExist()
	{
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
	 * @covers             Joomla\Language\LanguageFactory::setLanguageDirectory
	 * @expectedException  \InvalidArgumentException
	 */
	public function testSetLanguageDirectoryThrowsAnExceptionWhenAPathDoesNotExist()
	{
		$this->object->setLanguageDirectory(__DIR__ . '/negative-tester');
	}
}

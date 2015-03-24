<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\Stemmer;

/**
 * Test class for \Joomla\Language\Stemmer.
 */
class StemmerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox  Verify getInstance() returns an instance of the correct object
	 *
	 * @covers   Joomla\Language\Stemmer::getInstance
	 */
	public function testGetInstanceReturnsAnInstanceOfTheCorrectObject()
	{
		$this->assertInstanceOf('\\Joomla\\Language\\Stemmer\\Porteren', Stemmer::getInstance('porteren'));
	}

	/**
	 * @testdox  Verify getInstance() returns the cached object
	 *
	 * @covers   Joomla\Language\Stemmer::getInstance
	 */
	public function testGetInstanceReturnsTheCachedObject()
	{
		$firstInstance  = Stemmer::getInstance('porteren');
		$cachedInstance = Stemmer::getInstance('porteren');

		$this->assertSame($firstInstance, $cachedInstance);
	}

	/**
	 * @testdox  Verify getInstance() returns an instance of the correct object
	 *
	 * @covers             Joomla\Language\Stemmer::getInstance
	 * @expectedException  \RuntimeException
	 */
	public function testGetInstanceThrowsAnExceptionIfTheObjectDoesNotExist()
	{
		Stemmer::getInstance('unexisting');
	}
}

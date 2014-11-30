<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\Transliterate;

/**
 * Test class for Transliterate.
 */
class TransliterateTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var  Transliterate
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Transliterate;
	}

	/**
	 * Data provider for testUtf8_latin_to_ascii()
	 *
	 * @return  array
	 */
	public function dataProvider()
	{
		return array(
			array('Weiß', 'Weiss', 0),
			array('Goldmann', 'Goldmann', 0),
			array('Göbel', 'Goebel', 0),
			array('Weiss', 'Weiss', 0),
			array('Göthe', 'Goethe', 0),
			array('Götz', 'Goetz', 0),
			array('Weßling', 'Wessling', 0),
			array('Šíleně', 'Silene', 0),
			array('žluťoučký', 'zlutoucky', 0),
			array('Vašek', 'Vasek', 0),
			array('úpěl', 'upel', 0),
			array('olol', 'olol', 0),
			array('Göbel', 'Goebel', -1),
			array('Göbel', 'Göbel', 1)
		);
	}

	/**
	 * @testdox  Verify a UTF-8 string is transliterated correctly
	 *
	 * @param   string   $word    Word to transliterate
	 * @param   string   $result  Expected test result
	 * @param   integer  $case    Optionally specify upper or lower case. Default to 0 (both).
	 *
	 * @covers        Joomla\Language\Transliterate::utf8_latin_to_ascii
	 * @dataProvider  dataProvider
	 */
	public function testVerifyAUTF8StringIsTransliteratedCorrectly($word, $result, $case)
	{
		$this->assertEquals($result, $this->object->utf8_latin_to_ascii($word, $case));
	}
}

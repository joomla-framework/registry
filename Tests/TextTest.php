<?php
/**
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Language\Text;
use Joomla\Language\Language;
use Joomla\Test\TestHelper;

/**
 * Test class for \Joomla\Language\Text.
 *
 * @since  1.0
 */
class TextTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    Joomla\Language\Text
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
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Text(new Language('en-GB'));
	}

	/**
	 * Test...
	 *
	 * @covers Joomla\Language\Text::getLanguage
	 *
	 * @return  void
	 *
	 * @since   1.1.2
	 */
	public function testGetLanguage()
	{
		$this->assertInstanceOf('Joomla\\Language\\Language', $this->object->getLanguage());
	}

	/**
	 * Test...
	 *
	 * @covers Joomla\Language\Text::setLanguage
	 *
	 * @return  void
	 *
	 * @since   1.1.2
	 */
	public function testSetLanguage()
	{
		$this->assertInstanceOf(
			'Joomla\\Language\\Language',
			TestHelper::getValue($this->object, 'language')
		);
	}

	/**
	 * Test...
	 *
	 * @covers Joomla\Language\Text::_
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function test_()
	{
		$string = "fooobar's";
		$output = $this->object->_($string);

		$this->assertEquals($string, $output);

		$nStrings = count(TestHelper::getValue($this->object, 'strings'));
		$options = array('jsSafe' => true);
		$output = $this->object->_($string, $options);

		$this->assertEquals("fooobar\\'s", $output);
		$this->assertEquals(
			$nStrings,
			count(TestHelper::getValue($this->object, 'strings'))
		);

		$nStrings = count(TestHelper::getValue($this->object, 'strings'));
		$options = array('script' => true);
		$output = $this->object->_($string, $options);

		$this->assertEquals("fooobar's", $output);
		$this->assertEquals(
			$nStrings + 1,
			count(TestHelper::getValue($this->object, 'strings'))
		);

		$string = 'foo\\\\bar';
		$key = strtoupper($string);
		$output = $this->object->_($string, array('interpretBackSlashes' => true));

		$this->assertEquals('foo\\bar', $output);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Text::alt
	 * @todo    Implement testAlt().
	 *
	 * @return  void
	 */
	public function testAlt()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Text::plural
	 *
	 * @return  void
	 *
	 * @since   1.1.2
	 */
	public function testPlural()
	{
		$string = "bar's";

		// @todo change it to Text::plural($string);
		$output = $this->object->plural($string, 0);

		$this->assertEquals($string, $output);

		$nStrings = count(TestHelper::getValue($this->object, 'strings'));
		$options = array('jsSafe' => true);
		$output = $this->object->plural($string, 0, $options);

		$this->assertEquals("bar\\'s", $output);
		$this->assertCount(
			$nStrings,
			TestHelper::getValue($this->object, 'strings')
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Text::sprintf
	 *
	 * @return  void
	 *
	 * @since   1.1.2
	 */
	public function testSprintf()
	{
		$string = "foobar's";
		$output = $this->object->sprintf($string);

		$this->assertEquals($string, $output);

		$nStrings = count(TestHelper::getValue($this->object, 'strings'));
		$options = array('jsSafe' => true);
		$output = $this->object->sprintf($string, $options);

		$this->assertEquals("foobar\\'s", $output);
		$this->assertCount(
			$nStrings,
			TestHelper::getValue($this->object, 'strings')
		);

		$nStrings = count(TestHelper::getValue($this->object, 'strings'));
		$options = array('script' => true);
		$output = $this->object->sprintf($string, $options);

		$this->assertEquals("foobar's", $output);
		$this->assertCount(
			$nStrings + 1,
			TestHelper::getValue($this->object, 'strings')
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Text::printf
	 *
	 * @return  void
	 *
	 * @since   1.1.2
	 */
	public function testPrintf()
	{
		$string = "foobar";
		ob_start();
		$len = $this->object->printf($string);
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals($string, $output);
		$this->assertEquals(strlen($string), $len);

		$options = array('jsSafe' => false);
		ob_start();
		$len = $this->object->printf($string, $options);
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEquals($string, $output);
		$this->assertEquals(strlen($string), $len);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Language\Text::script
	 *
	 * @return  void
	 *
	 * @since   1.1.2
	 */
	public function testScript()
	{
		$string = 'foobar';
		$key = strtoupper($string);
		$strings = $this->object->script($string);

		$this->assertArrayHasKey($key, $strings);
		$this->assertEquals($string, $strings[$key]);

		$string = 'foo\\\\bar';
		$key = strtoupper($string);
		$strings = $this->object->script($string, array('interpretBackSlashes' => true));

		$this->assertArrayHasKey($key, $strings);
		$this->assertEquals('foo\\bar', $strings[$key]);

		$string = "foo\\bar's";
		$key = strtoupper($string);
		$strings = $this->object->script($string, array('jsSafe' => true));

		$this->assertArrayHasKey($key, $strings);
		$this->assertEquals("foo\\\\bar\\'s", $strings[$key]);
	}
}

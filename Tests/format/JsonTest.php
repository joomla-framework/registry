<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Format;

use Joomla\Registry\Format\Json;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Registry\Format\Json.
 */
class JsonTest extends TestCase
{
	/**
	 * @testdox  A data object is converted to a string
	 *
	 * @covers   Joomla\Registry\Format\Json
	 */
	public function testADataObjectIsConvertedToAString()
	{
		$class = new Json;

		$object = new \stdClass;
		$object->foo = 'bar';
		$object->quoted = '"stringwithquotes"';
		$object->booleantrue = true;
		$object->booleanfalse = false;
		$object->numericint = 42;
		$object->numericfloat = 3.1415;

		// A string that looks like an unicode sequence, should remain what it is: a string
		$object->unicodesequence = '\u0000';

		// The PHP registry format does not support nested objects
		$object->section = new \stdClass;
		$object->section->key = 'value';
		$object->array = ['nestedarray' => ['test1' => 'value1']];

		$string = '{"foo":"bar","quoted":"\"stringwithquotes\"",' .
			'"booleantrue":true,"booleanfalse":false,' .
			'"numericint":42,"numericfloat":3.1415,' .
			'"unicodesequence":"\\\\u0000",' .
			'"section":{"key":"value"},' .
			'"array":{"nestedarray":{"test1":"value1"}}' .
			'}';

		$decoded = json_decode($class->objectToString($object));

		// Ensures that the generated string respects the json syntax
		$errorMsg = 'JSON error decoding string.  Code: ' . json_last_error() . '; Message: ' . json_last_error_msg();

		$this->assertNotNull($decoded, $errorMsg);

		// Test basic object to string.
		$this->assertSame($string, $class->objectToString($object));
	}

	/**
	 * @testdox  A string is converted to a data object
	 *
	 * @covers   Joomla\Registry\Format\Json
	 * @uses     Joomla\Registry\Factory
	 * @uses     Joomla\Registry\Format\Ini
	 */
	public function testAStringIsConvertedToADataObject()
	{
		$class = new Json;

		$string1 = '{"title":"Joomla Framework","author":"Me","params":{"show_title":1,"show_abstract":0,"show_author":1,"categories":[1,2]}}';
		$string2 = "[section]\nfoo=bar";

		$object1 = new \stdClass;
		$object1->title = 'Joomla Framework';
		$object1->author = 'Me';
		$object1->params = new \stdClass;
		$object1->params->show_title = 1;
		$object1->params->show_abstract = 0;
		$object1->params->show_author = 1;
		$object1->params->categories = [1, 2];

		$object2 = new \stdClass;
		$object2->section = new \stdClass;
		$object2->section->foo = 'bar';

		$object3 = new \stdClass;
		$object3->foo = 'bar';

		// Test basic JSON string to object.
		$this->assertEquals(
			$class->stringToObject($string1, ['processSections' => false]),
			$object1,
			'The complex JSON string should convert into the appropriate object.'
		);

		// Test JSON format string without sections.
		$this->assertEquals(
			$class->stringToObject($string2, ['processSections' => false]),
			$object3,
			'The JSON string should convert into an object without sections.'
		);

		// Test JSON format string with sections.
		$this->assertEquals(
			$class->stringToObject($string2, ['processSections' => true]),
			$object2,
			'The JSON string should covert into an object with sections.'
		);
	}

	/**
	 * @testdox  A malformed JSON string causes an Exception to be thrown
	 *
	 * @covers   Joomla\Registry\Format\Json
	 */
	public function testAMalformedJsonStringCausesAnExceptionToBeThrown()
	{
		$this->expectException(\RuntimeException::class);

		(new Json)->stringToObject('{key:\'value\'');
	}

	/**
	 * @testdox  Validate data equality in converted objects
	 *
	 * @covers   Joomla\Registry\Format\Json
	 */
	public function testDataEqualityInConvertedObjects()
	{
		$class = new Json;

		$input = '{"title":"Joomla Framework","author":"Me","params":{"show_title":1,"show_abstract":0,"show_author":1,"categories":[1,2]}}';
		$object = $class->stringToObject($input);
		$output = $class->objectToString($object);

		$this->assertEquals($input, $output, 'Input and output data must be equal.');
	}

	/**
	 * @testdox  Validate an Exception is not thrown when a string is decoded to null and no error is reported
	 *
	 * @covers   Joomla\Registry\Format\Json
	 */
	public function testExceptionNotThrownWhenDecodedToNullWithoutError()
	{
		$this->assertInstanceOf('stdClass', (new Json)->stringToObject('{}'));
	}
}

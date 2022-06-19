<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Format;

use Joomla\Registry\Format\Yaml;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;

/**
 * Test class for \Joomla\Registry\Format\Yaml.
 */
class YamlTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  Yaml
	 */
	private $fixture;

	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	public function setUp(): void
	{
		$this->fixture = new Yaml;
	}

	/**
	 * @testdox  A data object is converted to a string
	 *
	 * @covers   Joomla\Registry\Format\Yaml
	 */
	public function testADataObjectIsConvertedToAString()
	{
		$object = (object) [
			'foo' => 'bar',
			'quoted' => '"stringwithquotes"',
			'booleantrue' => true,
			'booleanfalse' => false,
			'numericint' => 42,
			'numericfloat' => 3.1415,
			'section' => (object) ['key' => 'value'],
			'array' => (object) ['nestedarray' => (object) ['test1' => 'value1']]
		];

		$yaml = 'foo: bar
quoted: \'"stringwithquotes"\'
booleantrue: true
booleanfalse: false
numericint: 42
numericfloat: 3.1415
section:
    key: value
array:
    nestedarray: { test1: value1 }
';

		$this->assertEquals(
			str_replace(["\n", "\r"], '', trim($this->fixture->objectToString($object))),
			str_replace(["\n", "\r"], '', trim($yaml))
		);
	}

	/**
	 * @testdox  An array is converted to a string
	 *
	 * @covers   Joomla\Registry\Format\Yaml
	 */
	public function testAnArrayIsConvertedToAString()
	{
		$object = [
			'foo' => 'bar',
			'quoted' => '"stringwithquotes"',
			'booleantrue' => true,
			'booleanfalse' => false,
			'numericint' => 42,
			'numericfloat' => 3.1415,
			'section' => ['key' => 'value'],
			'array' => ['nestedarray' => ['test1' => 'value1']]
		];

		$yaml = 'foo: bar
quoted: \'"stringwithquotes"\'
booleantrue: true
booleanfalse: false
numericint: 42
numericfloat: 3.1415
section:
    key: value
array:
    nestedarray: { test1: value1 }
';

		$this->assertEquals(
			str_replace(["\n", "\r"], '', trim($this->fixture->objectToString($object))),
			str_replace(["\n", "\r"], '', trim($yaml))
		);
	}

	/**
	 * @testdox  A string is converted to a data object
	 *
	 * @covers   Joomla\Registry\Format\Yaml
	 */
	public function testAStringIsConvertedToADataObject()
	{
		$object = (object) [
			'foo' => 'bar',
			'quoted' => '"stringwithquotes"',
			'booleantrue' => true,
			'booleanfalse' => false,
			'numericint' => 42,
			'numericfloat' => 3.1415,
			'section' => (object) ['key' => 'value'],
			'array' => (object) ['nestedarray' => (object) ['test1' => 'value1']]
		];

		$yaml = 'foo: bar
quoted: \'"stringwithquotes"\'
booleantrue: true
booleanfalse: false
numericint: 42
numericfloat: 3.1415
section:
    key: value
array:
    nestedarray: { test1: value1 }
';
		$this->assertEquals($object, $this->fixture->stringToObject($yaml));
	}

	/**
	 * @testdox  Validate data equality in converted objects
	 *
	 * @covers   Joomla\Registry\Format\Yaml
	 */
	public function testDataEqualityInConvertedObjects()
	{
		$input = "foo: bar\nquoted: '\"stringwithquotes\"'\nbooleantrue: true\nbooleanfalse: false\nnumericint: 42\nnumericfloat: 3.1415\n" .
				"section:\n    key: value\narray:\n    nestedarray: { test1: value1 }\n";

		$object = $this->fixture->stringToObject($input);
		$output = $this->fixture->objectToString($object);

		$this->assertEquals($input, $output, 'Input and output data must be equal.');
	}
}

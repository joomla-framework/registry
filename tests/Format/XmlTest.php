<?php

/**
 * @copyright  Copyright (C) 2013 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Format;

use Joomla\Registry\Format\Xml;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Registry\Format\Xml.
 */
class XmlTest extends TestCase
{
    /**
     * @testdox  A data object is converted to a string
     *
     * @covers   \Joomla\Registry\Format\Xml
     */
    public function testADataObjectIsConvertedToAString()
    {
        $class = new Xml();

        $object               = new \stdClass();
        $object->foo          = 'bar';
        $object->quoted       = '"stringwithquotes"';
        $object->booleantrue  = true;
        $object->booleanfalse = false;
        $object->numericint   = 42;
        $object->numericfloat = 3.1415;
        $object->section      = new \stdClass();
        $object->section->key = 'value';
        $object->array        = ['nestedarray' => ['test1' => 'value1']];

        // Check for different PHP behavior of displaying boolean false in XML.
        $checkFalse = '<check/>' === \simplexml_load_string('<test/>')->addChild('check', false)->asXML(
        ) ? '/>' : '></node>';

        $string = "<?xml version=\"1.0\"?>\n<registry>" .
            '<node name="foo" type="string">bar</node>' .
            '<node name="quoted" type="string">"stringwithquotes"</node>' .
            '<node name="booleantrue" type="boolean">1</node>' .
            '<node name="booleanfalse" type="boolean"' . $checkFalse .
            '<node name="numericint" type="integer">42</node>' .
            '<node name="numericfloat" type="double">3.1415</node>' .
            '<node name="section" type="object">' .
            '<node name="key" type="string">value</node>' .
            '</node>' .
            '<node name="array" type="array">' .
            '<node name="nestedarray" type="array">' .
            '<node name="test1" type="string">value1</node>' .
            '</node>' .
            '</node>' .
            '</registry>' . "\n";

        // Test basic object to string.
        $this->assertSame($string, $class->objectToString($object));
    }

    /**
     * @testdox  A string is converted to a data object
     *
     * @covers   \Joomla\Registry\Format\Xml
     */
    public function testAStringIsConvertedToADataObject()
    {
        $class = new Xml();

        $object                = new \stdClass();
        $object->foo           = 'bar';
        $object->booleantrue   = true;
        $object->booleanfalse1 = false;
        $object->booleanfalse2 = false;
        $object->numericint    = 42;
        $object->numericfloat  = 3.1415;
        $object->section       = new \stdClass();
        $object->section->key  = 'value';
        $object->array         = ['test1' => 'value1'];

        $string = "<?xml version=\"1.0\"?>\n<registry>" .
            '<node name="foo" type="string">bar</node>' .
            '<node name="booleantrue" type="boolean">1</node>' .
            '<node name="booleanfalse1" type="boolean"></node>' .
            '<node name="booleanfalse2" type="boolean"/>' .
            '<node name="numericint" type="integer">42</node>' .
            '<node name="numericfloat" type="double">3.1415</node>' .
            '<node name="section" type="object">' .
            '<node name="key" type="string">value</node>' .
            '</node>' .
            '<node name="array" type="array">' .
            '<node name="test1" type="string">value1</node>' .
            '</node>' .
            '</registry>' . "\n";

        // Test basic object to string.
        $this->assertEquals($object, $class->stringToObject($string));
    }

    /**
     * @testdox  Validate data equality in converted objects
     *
     * @covers   \Joomla\Registry\Format\Xml
     */
    public function testDataEqualityInConvertedObjects()
    {
        $class = new Xml();

        // Check for different PHP behavior of displaying boolean false in XML.
        $checkFalse = '<check/>' === \simplexml_load_string('<test/>')->addChild('check', false)->asXML(
        ) ? '/>' : '></node>';

        $input = "<?xml version=\"1.0\"?>\n<registry>" .
            '<node name="foo" type="string">bar</node>' .
            '<node name="booleantrue" type="boolean">1</node>' .
            '<node name="booleanfalse" type="boolean"' . $checkFalse .
            '<node name="numericint" type="integer">42</node>' .
            '<node name="numericfloat" type="double">3.1415</node>' .
            '<node name="section" type="object">' .
            '<node name="key" type="string">value</node>' .
            '</node>' .
            '<node name="array" type="array">' .
            '<node name="test1" type="string">value1</node>' .
            '</node>' .
            '</registry>' . "\n";

        $object = $class->stringToObject($input);
        $output = $class->objectToString($object);

        $this->assertEquals($input, $output, 'Input and output data must be equal.');
    }

    #[TestDox('Values with special characters survive a round trip')]
    public function testValuesWithSpecialCharactersSurviveARoundTrip()
    {
        $class  = new Xml();
        $object = (object) [
            'ampersand' => 'x & y <z>',
            'entity'    => 'already &amp; escaped',
            'quotes'    => 'He said "it\'s"',
        ];

        $this->assertEquals($object, $class->stringToObject($class->objectToString($object)));
    }

    #[TestDox('Malformed XML throws an exception instead of an error')]
    public function testMalformedXmlThrowsAnException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Error parsing XML data');

        (new Xml())->stringToObject('<bad');
    }

    #[TestDox('Parse errors do not leak into the libxml error buffer')]
    public function testParseErrorsDoNotLeakIntoTheLibxmlErrorBuffer()
    {
        $previous = \libxml_use_internal_errors(false);

        try {
            (new Xml())->stringToObject('<bad');
        } catch (\RuntimeException $e) {
            // Expected
        }

        $this->assertFalse(\libxml_use_internal_errors($previous), 'The libxml error handling setting must be restored.');
        $this->assertSame([], \libxml_get_errors());
    }

    #[TestDox('An invalid root or node name is rejected')]
    #[DataProvider('invalidNameProvider')]
    public function testAnInvalidRootOrNodeNameIsRejected(string $option, mixed $name)
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Xml())->objectToString((object) ['foo' => 'bar'], [$option => $name]);
    }

    public static function invalidNameProvider(): \Generator
    {
        yield 'root name with markup' => ['name', 'registry/><injected'];
        yield 'root name with a space' => ['name', 'my registry'];
        yield 'empty root name' => ['name', ''];
        yield 'node name with markup' => ['nodeName', 'node><injected/'];
        yield 'node name starting with a digit' => ['nodeName', '1node'];
        yield 'node name that is not a string' => ['nodeName', ['node']];
    }

    #[TestDox('Custom root and node names are used')]
    public function testCustomRootAndNodeNamesAreUsed()
    {
        $this->assertSame(
            "<?xml version=\"1.0\"?>\n<data><value name=\"foo\" type=\"string\">bar</value></data>\n",
            (new Xml())->objectToString((object) ['foo' => 'bar'], ['name' => 'data', 'nodeName' => 'value'])
        );
    }
}

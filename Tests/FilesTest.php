<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Input\Tests;

use Joomla\Filter\InputFilter;
use Joomla\Input\Files;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Input\Files.
 */
class FilesTest extends TestCase
{
	/**
	 * @testdox  Tests the default constructor behavior
	 *
	 * @covers   Joomla\Input\Files
	 * @uses     Joomla\Input\Input
	 */
	public function test__constructDefaultBehaviour()
	{
		$instance = new Files;

		$this->assertSame($_FILES, TestHelper::getValue($instance, 'data'), 'The Files input defaults to the $_FILES superglobal');
		$this->assertInstanceOf(InputFilter::class, TestHelper::getValue($instance, 'filter'), 'The Input object should create an InputFilter if one is not provided');
	}

	/**
	 * @testdox  Tests the constructor with injected data
	 *
	 * @covers   Joomla\Input\Files
	 * @uses     Joomla\Input\Input
	 */
	public function test__constructDependencyInjection()
	{
		$src        = ['foo' => 'bar'];
		$mockFilter = $this->createMock(InputFilter::class);

		$instance = new Files($src, ['filter' => $mockFilter]);

		$this->assertSame($src, TestHelper::getValue($instance, 'data'));
		$this->assertSame($mockFilter, TestHelper::getValue($instance, 'filter'));
	}

	/**
	 * @testdox  Tests the data source is correctly read
	 *
	 * @covers   Joomla\Input\Files
	 * @uses     Joomla\Input\Input
	 */
	public function testGet()
	{
		$data = [
			'myfile'  => [
				'name'     => 'n',
				'type'     => 'ty',
				'tmp_name' => 'tm',
				'error'    => 'e',
				'size'     => 's'
			],
			'myfile2' => [
				'name'     => 'nn',
				'type'     => 'ttyy',
				'tmp_name' => 'ttmm',
				'error'    => 'ee',
				'size'     => 'ss'
			]
		];

		$mockFilter = $this->createMock(InputFilter::class);

		$instance = new Files($data, ['filter' => $mockFilter]);

		$this->assertEquals('foobar', $instance->get('myfile3', 'foobar'), 'The default value is returned if data does not exist.');

		$this->assertEquals(
			[
				'name'     => 'n',
				'type'     => 'ty',
				'tmp_name' => 'tm',
				'error'    => 'e',
				'size'     => 's'
			],
			$instance->get('myfile')
		);
	}

	/**
	 * @testdox  Tests a multi-level data source is correctly read
	 *
	 * @covers   Joomla\Input\Files
	 * @uses     Joomla\Input\Input
	 */
	public function testGetWithMultiLevelData()
	{
		$dataArr = ['first', 'second'];
		$data = [
			'myfile'  => [
				'name'     => $dataArr,
				'type'     => $dataArr,
				'tmp_name' => $dataArr,
				'error'    => $dataArr,
				'size'     => $dataArr
			]
		];

		$mockFilter = $this->createMock(InputFilter::class);

		$instance = new Files($data, ['filter' => $mockFilter]);

		$this->assertEquals(
			[
				[
					'name'     => 'first',
					'type'     => 'first',
					'tmp_name' => 'first',
					'error'    => 'first',
					'size'     => 'first'
				],
				[
					'name'     => 'second',
					'type'     => 'second',
					'tmp_name' => 'second',
					'error'    => 'second',
					'size'     => 'second'
				]
			],
			$instance->get('myfile')
		);
	}

	/**
	 * @testdox  Tests the data source cannot be modified
	 *
	 * @covers   Joomla\Input\Files
	 * @uses     Joomla\Input\Input
	 */
	public function testSet()
	{
		$instance = new Files;
		$instance->set('foo', 'bar');

		$this->assertFalse($instance->exists('foo'));
	}
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\View\Tests;

use Joomla\View\JsonView;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\View\JsonView
 */
class JsonViewTest extends TestCase
{
	/**
	 * Test object
	 *
	 * @var  JsonView
	 */
	private $object;

	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->object = new JsonView;
	}

	public function testEnsureRenderReturnsTheDataInJsonFormat()
	{
		$this->object->setData(['test' => 'value']);

		$this->assertSame(json_encode($this->object->getData()), $this->object->render());
	}
}

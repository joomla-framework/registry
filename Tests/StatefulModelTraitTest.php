<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Model\Tests;

use Joomla\Model\StatefulModelTrait;
use Joomla\Registry\Registry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests for \Joomla\Model\StatefulModelTrait.
 */
class StatefulModelTraitTest extends TestCase
{
	/**
	 * @testdox  Calling getState() without a state set will throw an Exception
	 *
	 * @covers   Joomla\Model\StatefulModelTrait
	 */
	public function testGetStateException()
	{
		$this->expectException(\UnexpectedValueException::class);

		/** @var StatefulModelTrait|MockObject $object */
		$object = $this->getObjectForTrait(StatefulModelTrait::class);
		$object->getState();
	}

	/**
	 * @testdox  A Registry representing the state is set and retrieved
	 *
	 * @covers   Joomla\Model\StatefulModelTrait
	 */
	public function testSetAndgetState()
	{
		/** @var StatefulModelTrait|MockObject $object */
		$object = $this->getObjectForTrait(StatefulModelTrait::class);

		/** @var Registry|MockObject $state */
		$state = $this->createMock(Registry::class);

		$object->setState($state);

		$this->assertSame($state, $object->getState());
	}
}

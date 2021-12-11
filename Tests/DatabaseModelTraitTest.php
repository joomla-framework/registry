<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Model\Tests;

use Joomla\Database\DatabaseInterface;
use Joomla\Model\DatabaseModelTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests for \Joomla\Model\DatabaseModelTrait.
 */
class DatabaseModelTraitTest extends TestCase
{
	/**
	 * @testdox  Calling getDb() without a DatabaseDriver set will throw an Exception
	 *
	 * @covers   Joomla\Model\DatabaseModelTrait
	 */
	public function testGetDbException()
	{
		$this->expectException(\UnexpectedValueException::class);

		/** @var DatabaseModelTrait|MockObject $object */
		$object = $this->getObjectForTrait(DatabaseModelTrait::class);
		$object->getDb();
	}

	/**
	 * @testdox  A DatabaseDriver is set and retrieved
	 *
	 * @covers   Joomla\Model\DatabaseModelTrait
	 */
	public function testSetAndGetDb()
	{
		/** @var DatabaseModelTrait|MockObject $object */
		$object = $this->getObjectForTrait(DatabaseModelTrait::class);

		/** @var DatabaseInterface|MockObject $db */
		$db = $this->createMock(DatabaseInterface::class);

		$object->setDb($db);

		$this->assertSame($db, $object->getDb());
	}
}

<?php
/**
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\DI\Tests;

use Joomla\DI\Container;
use Joomla\DI\ContainerAwareTrait;
use Joomla\DI\Exception\ContainerNotFoundException;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests for ContainerAwareTrait class.
 */
class ContainerAwareTraitTest extends TestCase
{
	/**
	 * @var ContainerAwareTrait
	 */
	protected $object;

	/**
	 * @testdox  Container can be set with setContainer()
	 *
	 * @covers   Joomla\DI\ContainerAwareTrait
	 * @uses     Joomla\DI\Container
	 */
	public function testGetContainer()
	{
		$container = new Container();

		$trait     = $this->getObjectForTrait(ContainerAwareTrait::class);
		$trait->setContainer($container);

		$this->assertSame($container, TestHelper::getValue($trait, 'container'));
	}

	/**
	 * @testdox  getContainer() throws an ContainerNotFoundException, if no container is set
	 *
	 * @covers   Joomla\DI\ContainerAwareTrait
	 */
	public function testGetContainerException()
	{
		$this->expectException(ContainerNotFoundException::class);

		$trait = $this->getObjectForTrait(ContainerAwareTrait::class);

		TestHelper::invoke($trait, 'getContainer');
	}
}

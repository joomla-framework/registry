<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests\Service;

use Joomla\DI\Container;
use Joomla\Language\Service\LanguageFactoryProvider;
use Joomla\Registry\Registry;

/**
 * Test class for Joomla\Language\Service\LanguageFactoryProvider.
 */
class LanguageFactoryProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * DI Container for testing
	 *
	 * @var  Container
	 */
	private $container;

	/**
	 * Path to language folder used for testing
	 *
	 * @var  string
	 */
	private $testPath;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->testPath  = __DIR__ . '/data';

		// Create a DI container for testing
		$this->container = new Container;
		$config = new Registry;
		$config->set('language.basedir', $this->testPath);
		$this->container->set('config', $config);
	}

	/**
	 * @testdox  Verify that the LanguageFactoryProvider returns a LanguageFactory object
	 *
	 * @covers   Joomla\Language\Service\LanguageFactoryProvider::register
	 * @uses     Joomla\Language\LanguageFactory
	 */
	public function testVerifyTheLanguageObjectIsRegisteredToTheContainer()
	{
		$this->container->registerServiceProvider(new LanguageFactoryProvider);

		$this->assertInstanceOf(
			'\\Joomla\\Language\\LanguageFactory',
			$this->container->get('Joomla\\Language\\LanguageFactory')
		);
	}
}

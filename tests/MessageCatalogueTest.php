<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\MessageCatalogue;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Language\MessageCatalogue.
 */
class MessageCatalogueTest extends TestCase
{
	/**
	 * Test message catalogue
	 *
	 * @var  MessageCatalogue
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->object = new MessageCatalogue('en-GB');
	}

	/**
	 * @testdox  Verify the catalogue's language is returned
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testTheCataloguesLanguageIsReturned()
	{
		$this->assertSame('en-GB', $this->object->getLanguage());
	}

	/**
	 * @testdox  Verify that a single message is added to the catalogue
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testASingleMessageIsAddedToTheCatalogue()
	{
		$this->object->addMessage('foo', 'bar');

		$this->assertSame(['FOO' => 'bar'], $this->object->getMessages());
	}

	/**
	 * @testdox  Verify that multiple messages are added to the catalogue
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testMultipleMessagesAreAddedToTheCatalogue()
	{
		$messages = [
			'foo' => 'bar',
			'goo' => 'car',
		];

		$this->object->addMessages($messages);

		$this->assertSame(array_change_key_case($messages, CASE_UPPER), $this->object->getMessages());
	}

	/**
	 * @testdox  Verify that the catalogue accurately reports key presence on this catalogue
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testTheCatalogueAccuratelyReportsKeyPresenceOnThisCatalogue()
	{
		$this->object->addMessage('foo', 'bar');

		$fallbackCatalogue = new MessageCatalogue('en-US', ['goo' => 'car']);

		$this->object->setFallbackCatalogue($fallbackCatalogue);

		$this->assertTrue($this->object->definesMessage('foo'));
		$this->assertFalse($this->object->definesMessage('goo'));
	}

	/**
	 * @testdox  Verify that a message is retrieved from the catalogue when the key is registered
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testAMessageIsRetrievedFromTheCatalogueWhenTheKeyIsRegistered()
	{
		$this->object->addMessage('foo', 'bar');

		$this->assertSame('bar', $this->object->getMessage('foo'));
	}

	/**
	 * @testdox  Verify that the key is returned when it does not exist in the catalogue
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testTheKeyIsReturnedWhenItDoesNotExistInTheCatalogue()
	{
		$this->assertSame('FOO', $this->object->getMessage('foo'));
	}

	/**
	 * @testdox  Verify that a message is retrieved from the fallback catalogue when the key is registered
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testAMessageIsRetrievedFromTheFallbackCatalogueWhenTheKeyIsRegistered()
	{
		$fallbackCatalogue = new MessageCatalogue('en-US', ['foo' => 'bar']);

		$this->object->setFallbackCatalogue($fallbackCatalogue);

		$this->assertSame('bar', $this->object->getMessage('foo'));
	}

	/**
	 * @testdox  Verify that the catalogue's messages are returned
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testTheCataloguesMessagesAreReturned()
	{
		$this->assertSame([], $this->object->getMessages());
	}

	/**
	 * @testdox  Verify that two catalogues are merged
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testTwoCataloguesAreMerged()
	{
		$this->object->addMessage('foo', 'bar');

		$secondCatalogue = new MessageCatalogue('en-GB', ['goo' => 'car']);

		$this->object->mergeCatalogue($secondCatalogue);

		$this->assertSame(['FOO' => 'bar', 'GOO' => 'car'], $this->object->getMessages());
	}

	/**
	 * @testdox  Verify that two catalogues are not merged when the language codes differ
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testTwoCataloguesAreNotMergedWhenTheLanguageCodesDiffer()
	{
		$this->expectException(\LogicException::class);
		$this->expectExceptionMessage('Cannot merge a catalogue that does not have the same language code.');

		$this->object->addMessage('foo', 'bar');

		$secondCatalogue = new MessageCatalogue('en-US', ['goo' => 'car']);

		$this->object->mergeCatalogue($secondCatalogue);
	}

	/**
	 * @testdox  Verify that the catalogue accurately reports key presence
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testTheCatalogueAccuratelyReportsKeyPresence()
	{
		$this->object->addMessage('foo', 'bar');

		$this->assertTrue($this->object->hasMessage('foo'));
		$this->assertFalse($this->object->hasMessage('goo'));
	}

	/**
	 * @testdox  Verify that the catalogue accurately reports key presence from a fallback catalogue
	 *
	 * @covers   Joomla\Language\MessageCatalogue
	 */
	public function testTheCatalogueAccuratelyReportsKeyPresenceFromAFallbackCatalogue()
	{
		$fallbackCatalogue = new MessageCatalogue('en-US', ['foo' => 'bar']);

		$this->object->setFallbackCatalogue($fallbackCatalogue);

		$this->assertTrue($this->object->hasMessage('foo'));
		$this->assertFalse($this->object->hasMessage('goo'));
	}
}

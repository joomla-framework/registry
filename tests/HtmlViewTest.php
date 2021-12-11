<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\View\Tests;

use Joomla\Renderer\RendererInterface;
use Joomla\View\HtmlView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\View\HtmlView
 */
class HtmlViewTest extends TestCase
{
	/**
	 * Mock renderer
	 *
	 * @var  MockObject|RendererInterface
	 */
	private $mockRenderer;

	/**
	 * Test object
	 *
	 * @var  HtmlView
	 */
	private $object;

	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->mockRenderer = $this->createMock(RendererInterface::class);
		$this->object       = new HtmlView($this->mockRenderer);
	}

	public function testEnsureTheConstructorSetsTheValuesCorrectly()
	{
		$this->assertSame($this->mockRenderer, $this->object->getRenderer());
	}

	public function testEnsureMagicToStringMethodRendersTheView()
	{
		$this->mockRenderer->expects($this->any())
			->method('render')
			->willReturn('Rendered View');

		$this->assertSame('Rendered View', (string) $this->object);
	}

	public function testEnsureRenderReturnsTheDataReceivedFromTheRenderer()
	{
		$this->assertEmpty($this->object->render());
	}

	public function testTheViewLayoutCanBeManaged()
	{
		$this->assertSame($this->object, $this->object->setLayout('layout'));
		$this->assertSame('layout', $this->object->getLayout());
	}

	public function testTheViewRendererCanBeManaged()
	{
		$renderer = $this->createMock(RendererInterface::class);

		$this->assertSame($this->object, $this->object->setRenderer($renderer));
		$this->assertSame($renderer, $this->object->getRenderer());
	}
}

<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Profiler\Tests;

use Joomla\Profiler\ProfilerRendererInterface;
use Joomla\Profiler\Renderer\DefaultRenderer;
use Joomla\Profiler\ProfilePoint;
use Joomla\Profiler\Profiler;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Profiler\Profiler.
 */
class ProfilerTest extends TestCase
{
	/**
	 * @var  Profiler
	 */
	private $instance;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->instance = new Profiler('test');
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 */
	public function testTheProfilerIsInstantiatedCorrectly()
	{
		$this->assertSame('test', $this->instance->getName());
		$this->assertInstanceOf(DefaultRenderer::class, $this->instance->getRenderer());
		$this->assertEmpty($this->instance->getPoints());
		$this->assertSame(false, TestHelper::getValue($this->instance, 'memoryRealUsage'));
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerIsInstantiatedCorrectlyWithInjectedDependencies()
	{
		$renderer = new DefaultRenderer;
		$pointOne = new ProfilePoint('start');
		$pointTwo = new ProfilePoint('two', 1, 1);
		$points   = [$pointOne, $pointTwo];

		$profiler = new Profiler('bar', $renderer, $points, true);

		$this->assertSame('bar', $profiler->getName());
		$this->assertSame($renderer, $profiler->getRenderer());
		$this->assertSame($points, $profiler->getPoints());
		$this->assertSame(true, TestHelper::getValue($profiler, 'memoryRealUsage'));
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerRegistersInjectedPointsCorrectly()
	{
		$point    = new ProfilePoint('start');
		$profiler = new Profiler('bar', null, [$point]);

		$this->assertTrue($profiler->hasPoint('start'));
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerCannotRegisterMultipleInjectedPointsWithTheSameName()
	{
		$this->expectException(\InvalidArgumentException::class);

		$point1   = new ProfilePoint('start');
		$point2   = new ProfilePoint('start');
		$profiler = new Profiler('bar', null, [$point1, $point2]);
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerCannotRegisterInjectedPointsNotImplementingThePointInterface()
	{
		$this->expectException(\InvalidArgumentException::class);

		$point1   = new ProfilePoint('start');
		$point2   = new \stdClass;
		$profiler = new Profiler('bar', null, [$point1, $point2]);
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 */
	public function testTheProfilerNameIsReturned()
	{
		$this->assertEquals('test', $this->instance->getName());
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerMarksASinglePoint()
	{
		$this->instance->mark('one');

		$this->assertTrue($this->instance->hasPoint('one'));
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerMarksMultiplePoints()
	{
		$this->instance->mark('one');
		$this->instance->mark('two');

		$this->assertTrue($this->instance->hasPoint('one'));
		$this->assertTrue($this->instance->hasPoint('two'));

		// Assert the first point has a time and memory = 0
		$firstPoint = $this->instance->getPoint('one');

		$this->assertSame(0.0, $firstPoint->getTime());
		$this->assertSame(0, $firstPoint->getMemoryBytes());

		// Assert the other point has a time and memory > 0
		$secondPoint = $this->instance->getPoint('two');

		$this->assertGreaterThan(0, $secondPoint->getTime());
		$this->assertGreaterThan(0, $secondPoint->getMemoryBytes());
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerCannotMarkMultiplePointsWithTheSameName()
	{
		$this->expectException(\LogicException::class);

		$this->instance->mark('test');
		$this->instance->mark('test');
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerChecksIfAPointHasBeenAdded()
	{
		$this->assertFalse($this->instance->hasPoint('test'));

		$this->instance->mark('test');

		$this->assertTrue($this->instance->hasPoint('test'));
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerRetrievesTheRequestedPoint()
	{
		$this->assertNull($this->instance->getPoint('foo'));

		$this->instance->mark('start');

		$point = $this->instance->getPoint('start');

		$this->assertInstanceOf(ProfilePoint::class, $point);
		$this->assertEquals('start', $point->getName());
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerMeasuresTheTimeBetweenTwoPoints()
	{
		$first  = new ProfilePoint('start');
		$second = new ProfilePoint('stop', 1.5);

		$profiler = new Profiler('test', null, [$first, $second]);

		$this->assertSame(1.5, $profiler->getTimeBetween('start', 'stop'));
		$this->assertSame(1.5, $profiler->getTimeBetween('stop', 'start'));
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerCannotMeasureTimeBetweenTwoPointsIfTheSecondPointDoesNotExist()
	{
		$this->expectException(\LogicException::class);

		$first    = new ProfilePoint('start');
		$profiler = new Profiler('test', null, [$first]);

		$profiler->getTimeBetween('start', 'bar');
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerCannotMeasureTimeBetweenTwoPointsIfTheFirstPointDoesNotExist()
	{
		$this->expectException(\LogicException::class);

		$first    = new ProfilePoint('start');
		$profiler = new Profiler('test', null, [$first]);

		$profiler->getTimeBetween('foo', 'start');
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerMeasuresTheMemoryUsedBetweenTwoPoints()
	{
		$first  = new ProfilePoint('start');
		$second = new ProfilePoint('stop', 0, 1000);

		$profiler = new Profiler('test', null, [$first, $second]);

		$this->assertSame(1000, $profiler->getMemoryBytesBetween('start', 'stop'));
		$this->assertSame(1000, $profiler->getMemoryBytesBetween('stop', 'start'));
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerCannotMeasureMemoryBetweenTwoPointsIfTheSecondPointDoesNotExist()
	{
		$this->expectException(\LogicException::class);

		$first    = new ProfilePoint('start');
		$profiler = new Profiler('test', null, [$first]);

		$profiler->getMemoryBytesBetween('start', 'bar');
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerCannotMeasureMemoryBetweenTwoPointsIfTheFirstPointDoesNotExist()
	{
		$this->expectException(\LogicException::class);

		$first    = new ProfilePoint('start');
		$profiler = new Profiler('test', null, [$first]);

		$profiler->getMemoryBytesBetween('foo', 'start');
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 */
	public function testTheProfilerReturnsThePeakMemoryUse()
	{
		$this->assertNull($this->instance->getMemoryPeakBytes());
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 */
	public function testTheProfilerReturnsTheMarkedPoints()
	{
		$this->assertEmpty($this->instance->getPoints());
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 */
	public function testTheProfilerCanHaveARendererInjected()
	{
		$renderer = new DefaultRenderer;

		$this->assertSame($this->instance, $this->instance->setRenderer($renderer), 'The setRenderer method has a fluent interface');
		$this->assertSame($renderer, $this->instance->getRenderer());
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 */
	public function testTheProfilerReturnsTheRenderer()
	{
		$this->assertInstanceOf(DefaultRenderer::class, $this->instance->getRenderer());
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 */
	public function testTheProfilerRendersItsData()
	{
		$mockedRenderer = $this->createMock(ProfilerRendererInterface::class);
		$mockedRenderer->expects($this->once())
			->method('render')
			->with($this->instance);

		$this->instance->setRenderer($mockedRenderer);

		$this->instance->render();
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 */
	public function testTheProfilerCanBeCastToAString()
	{
		$mockedRenderer = $this->createMock(ProfilerRendererInterface::class);
		$mockedRenderer->expects($this->once())
			->method('render')
			->with($this->instance)
			->willReturn('Rendered profile');

		$this->instance->setRenderer($mockedRenderer);

		$this->assertSame('Rendered profile', (string) $this->instance);
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerReturnsAnIterator()
	{
		// Create 3 points.
		$first  = new ProfilePoint('test');
		$second = new ProfilePoint('second', 1.5, 1000);
		$third  = new ProfilePoint('third', 2.5, 2000);
		$points = array($first, $second, $third);

		// Create a profiler and inject the points.
		$profiler = new Profiler('test', null, $points);

		$this->assertInstanceOf(\ArrayIterator::class, $profiler->getIterator());
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerCanBeCounted()
	{
		$this->assertCount(0, $this->instance);

		$this->instance->mark('start');
		$this->instance->mark('foo');
		$this->instance->mark('end');

		$this->assertCount(3, $this->instance);
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerStartTimeAndMemoryCanBeSet()
	{
		$time   = microtime(true);
		$memory = memory_get_usage(false);

		$this->instance->setStart($time, $memory);

		$this->assertSame($time, TestHelper::getValue($this->instance, 'startTimeStamp'));
		$this->assertSame($memory, TestHelper::getValue($this->instance, 'startMemoryBytes'));
		$this->assertCount(1, $this->instance);
	}

	/**
	 * @covers  Joomla\Profiler\Profiler
	 * @uses    Joomla\Profiler\ProfilePoint
	 */
	public function testTheProfilerStartTimeAndMemoryCannotBeChangedIfAPointHasBeenMarked()
	{
		$this->expectException(\RuntimeException::class);

		$time   = microtime(true);
		$memory = memory_get_usage(false);

		$this->instance->mark('test');
		$this->instance->setStart($time, $memory);
	}
}

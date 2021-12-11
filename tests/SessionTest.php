<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session\Tests;

use Joomla\Event\DispatcherInterface;
use Joomla\Input\Input;
use Joomla\Session\Session;
use Joomla\Session\Storage\RuntimeStorage;
use Joomla\Session\Validator\AddressValidator;
use Joomla\Session\Validator\ForwardedValidator;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Session\Session.
 */
class SessionTest extends TestCase
{
	/**
	 * Session object for testing
	 *
	 * @var  Session
	 */
	private $session;

	/**
	 * Storage object for testing
	 *
	 * @var  RuntimeStorage
	 */
	private $storage;

	/**
	 * {@inheritdoc}
	 */
	protected function setUp(): void
	{
		$mockInput = $this->getMockBuilder(Input::class)
			->setMethods(['get'])
			->getMock();

		// Mock the Input object internals
		$mockServerInput = $this->getMockBuilder(Input::class)
			->setMethods(['get', 'set'])
			->getMock();

		$inputInternals = [
			'server' => $mockServerInput,
		];

		TestHelper::setValue($mockInput, 'inputs', $inputInternals);

		$this->storage = new RuntimeStorage;
		$this->session = new Session($this->storage);
		$this->session->addValidator(new AddressValidator($mockInput, $this->session));
		$this->session->addValidator(new ForwardedValidator($mockInput, $this->session));
	}

	/**
	 * Data provider for set tests
	 *
	 * @return  \Generator
	 */
	public function setProvider(): \Generator
	{
		yield ['joomla', 'rocks'];
		yield ['joomla.framework', 'too much awesomeness'];
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateASessionObjectIsCreatedCorrectly()
	{
		// Build a mock event dispatcher
		$mockDispatcher = $this->getMockBuilder(DispatcherInterface::class)->getMock();

		$session = new Session($this->storage, $mockDispatcher);

		// The state should be inactive
		$this->assertSame('inactive', $session->getState());

		// And the session should not be active
		$this->assertFalse($session->isActive());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateASessionStartsCorrectly()
	{
		// There shouldn't be an ID yet
		$this->assertEmpty($this->session->getId());

		// The session should successfully start
		$this->session->start();
		$this->assertTrue($this->session->isStarted());

		// There should now be an ID
		$this->assertNotEmpty($this->session->getId());

		// And the session should be active
		$this->assertTrue($this->session->isActive());

		// As well as new
		$this->assertTrue($this->session->isNew());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\SessionEvent
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateTheDispatcherIsTriggeredWhenTheSessionIsStarted()
	{
		// Build a mock event dispatcher
		$mockDispatcher = $this->getMockBuilder(DispatcherInterface::class)->getMock();
		$mockDispatcher->expects($this->once())
			->method('dispatch');

		$this->session->setDispatcher($mockDispatcher);

		// The session should successfully start
		$this->session->start();
		$this->assertTrue($this->session->isStarted());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateAnInjectedSessionIdIsUsedWhenTheSessionStarts()
	{
		$mockId = '1234abcd';

		// Inject our ID
		$this->session->setId($mockId);

		// The session should successfully start
		$this->session->start();

		// The ID should match our injected value
		$this->assertSame($mockId, $this->session->getId());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateAnInjectedSessionNameIsUsedWhenTheSessionStarts()
	{
		$mockName = 'TestSessionName';

		// Inject our name
		$this->session->setName($mockName);

		// The session should successfully start
		$this->session->start();

		// The ID should match our injected value
		$this->assertSame($mockName, $this->session->getName());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateTheSessionTokenIsRetrieved()
	{
		$firstToken = $this->session->getToken();

		$this->assertSame(32, strlen($this->session->getToken()), 'A 32-character token is generated');
		$this->assertNotSame($firstToken, $this->session->getToken(true), 'A new token is generated');
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateTheSessionTokenIsValid()
	{
		$token = $this->session->getToken();

		$this->assertTrue($this->session->hasToken($token));

		// Invalid token not forcing the session to an expired state
		$state = $this->session->getState();
		$this->assertFalse($this->session->hasToken('not-the-token', false));
		$this->assertSame($state, $this->session->getState());

		// Invalid token forcing the session to an expired state
		$this->assertFalse($this->session->hasToken('not-the-token', true));
		$this->assertSame('expired', $this->session->getState());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateAnIteratorIsReturned()
	{
		$this->assertInstanceOf(\ArrayIterator::class, $this->session->getIterator());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateTheCorrectValueIsReturnedWhenGetIsCalled()
	{
		// Default return null
		$this->assertNull($this->session->get('foo'));

		// Return the specified default
		$this->assertSame('bar', $this->session->get('foo', 'bar'));
	}

	/**
	 * @param   string  $key    The key to set
	 * @param   string  $value  The value to set
	 *
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 *
	 * @dataProvider  setProvider
	 */
	public function testValidateAValueIsCorrectlyStoredToTheSession($key, $value)
	{
		$this->session->set($key, $value);
		$this->assertSame($value, $this->session->get($key));
	}

	/**
	 * @param   string  $key    The key to set
	 * @param   string  $value  The value to set
	 *
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 *
	 * @dataProvider  setProvider
	 */
	public function testValidateTheKeyIsCorrectlyCheckedForExistence($key, $value)
	{
		$this->session->set($key, $value);
		$this->assertTrue($this->session->has($key));
		$this->assertFalse($this->session->has($key . '.fake.ending'));
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateAKeyIsCorrectlyRemovedFromTheStore()
	{
		$this->session->set('foo', 'bar');
		$this->assertTrue($this->session->has('foo'));

		$this->session->remove('foo');
		$this->assertFalse($this->session->has('foo'));
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateAllDataIsReturnedFromTheSessionStore()
	{
		// Set some data into our session
		$this->session->set('foo', 'bar');
		$this->session->set('joomla.framework', 'is awesome');

		$this->assertArrayHasKey(
			'joomla.framework',
			$this->session->all()
		);

		// Now clear the data
		$this->session->clear();
		$this->assertEmpty($this->session->all());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateTheSessionIsCorrectlyDestroyed()
	{
		// First start a session to destroy it
		$this->session->start();

		// Grab the session ID to check in a moment
		$sessionId = $this->session->getId();

		// And add some data to validate it is cleared
		$this->session->set('foo', 'bar');

		// Now destroy the session
		$this->assertTrue($this->session->destroy());

		// Validate the destruction
		$this->assertNotSame($sessionId, $this->session->getId());
		$this->assertArrayNotHasKey('foo', $this->session->all());
		$this->assertSame('destroyed', $this->session->getState());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\SessionEvent
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateTheSessionIsCorrectlyRestarted()
	{
		// Build a mock event dispatcher
		$mockDispatcher = $this->getMockBuilder(DispatcherInterface::class)->getMock();

		$this->session->setDispatcher($mockDispatcher);
		$this->session->start();

		// Grab the session ID to check in a moment
		$sessionId = $this->session->getId();

		// And add some data to validate it is carried forward
		$this->session->set('foo', 'bar');

		// Now restart the session
		$mockDispatcher->expects($this->once())
			->method('dispatch');
		$this->assertTrue($this->session->restart());

		// Validate the restart
		$this->assertNotSame($sessionId, $this->session->getId());
		$this->assertArrayHasKey('foo', $this->session->all());
		$this->assertSame('active', $this->session->getState());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateTheSessionIsCorrectlyForkedWithoutDestruction()
	{
		// First make sure an inactive session cannot be forked
		$this->assertFalse($this->session->fork());

		$this->session->start();

		// Grab the session ID to check in a moment
		$sessionId = $this->session->getId();

		// And add some data to validate it is carried forward
		$this->session->set('foo', 'bar');

		// Now fork the session
		$this->assertTrue($this->session->fork());

		// Validate the fork
		$this->assertSame($sessionId, $this->session->getId());
		$this->assertArrayHasKey('foo', $this->session->all());
		$this->assertSame('active', $this->session->getState());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateTheSessionIsCorrectlyForkedWithDestruction()
	{
		$this->session->start();

		// Grab the session ID to check in a moment
		$sessionId = $this->session->getId();

		// And add some data to validate it is carried forward
		$this->session->set('foo', 'bar');

		// Now fork the session
		$this->assertTrue($this->session->fork(true));

		// Validate the fork
		$this->assertNotSame($sessionId, $this->session->getId());
		$this->assertArrayHasKey('foo', $this->session->all());
		$this->assertSame('active', $this->session->getState());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateTheSessionIsCorrectlyClosed()
	{
		$this->session->start();

		// Now close the session
		$this->session->close();

		// Validate the closure
		$this->assertSame('closed', $this->session->getState());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateThatSessionGarbageCollectionIsPerformed()
	{
		$this->session->start();

		$this->assertSame(0, $this->session->gc());
	}

	/**
	 * @covers  Joomla\Session\Session
	 * @uses    Joomla\Session\Storage\RuntimeStorage
	 * @uses    Joomla\Session\Validator\AddressValidator
	 * @uses    Joomla\Session\Validator\ForwardedValidator
	 */
	public function testValidateThatSessionIsAborted()
	{
		$this->session->start();

		$this->assertTrue($this->session->abort());
	}
}

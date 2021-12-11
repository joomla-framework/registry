## Overview

The Session package provides an interface for managing sessions within an application. The [`Joomla\Session\Session`](classes/Session.md) class is the base object within the package and serves as the primary API for managing a session.

### Creating your Session object
The `Session` class constructor takes 1 compulsory and 3 optional parameters:

```php
/**
 * @param   StorageInterface     $store       A StorageInterface implementation
 * @param   DispatcherInterface  $dispatcher  DispatcherInterface for the session to use.
 * @param   array                $options     Optional parameters
 */
public function __construct(StorageInterface $store = null, DispatcherInterface $dispatcher = null, array $options = [])
```

#### Storage Interface
The `Joomla\Session\StorageInterface` defines an object which represents a data store for session data. For more about this please read the [StorageInterface documentation](classes/StorageInterface.md).

#### Validator Interface
The `Joomla\Session\ValidatorInterface` defines an object which represents a validation check for the active session. For more about this please read the [ValidatorInterface documentation](classes/ValidatorInterface.md).

Use of validators is not required by the `Joomla\Session\SessionInterface`, but is supported in `Joomla\Session\Session` through the `addValidator()` method; therefore you should review your implementation to determine if validators are supported.

#### Dispatcher Interface
The `Joomla\Session\Session` class triggers events on session start and session restart. You can inject a `Joomla\Event\DispatcherInterface` implementation to use these events in your application. For more information on the Event Dispatcher package please read the [Joomla Event Package Documentation](https://github.com/joomla-framework/event)

#### Array of Options
The session will take an array of options. The following keys are recognised:

* ```name```: Will set the name of the session into the StorageInterface object
* ```id```: Will set the ID of the session into the StorageInterface object
* ```expire``` (default 900 seconds) Will be used to set the expiry time for the session

### Starting a session

A session can be started by calling the `start()` method.

```php
use Joomla\Session\Session;

$session = new Session;
$session->start();
```

This method is suitable for starting a new session or resuming a session if a session ID has already been assigned and stored in a session cookie.

If you injected an event dispatcher into the `Session` class then a [`Joomla\Session\SessionEvent`](classes/SessionEvent.md) for the `SessionEvents::START` event will be triggered.

Note that the `Joomla\Session\Session` class supports lazily started sessions and this method does NOT need to be explicitly called in your application, the first attempt to read from or write to the session data store will start the session if necessary.

### Closing a session
An existing session can be closed by calling the `close()` method. This will write all your session data through your storage handler.

```php
use Joomla\Session\Session;

$session = new Session;
$session->start();

// DO THINGS WITH SESSION

$session->close();
```

### The Session State
You can view the status of the session at any time by calling the `getState()` method. This will return one of the following strings:

* inactive
* active
* expired
* destroyed
* closed
* error

```php
use Joomla\Session\Session;

$session = new Session;
$session->getState();

// RETURNS: inactive

$session->start();
$session->getState()

// RETURNS: active
```

There is a further helper function `isStarted()` that tells you if the session has been started.

### Data in the session
The `Joomla\Session\SessionInterface` contains several methods to help you manage the data in your session.

#### Setting Data
You can set data into the session using the `set()` method. This method takes two parameters - the name of the variable you want to store and the value of that variable:

```php
use Joomla\Session\Session;

$session = new Session;
$session->start();

$session->set('foo', 'bar');

echo $_SESSION['foo']

// Assuming we are using the Native Storage Handler: RESULT: BAR
```

#### Getting Data
You can retrieve data set into the session using the `get()` function. This method also takes two parameters - the name of the variable you want to retrieve and the default value of that variable (null by default)

```php
use Joomla\Session\Session;

$session = new Session;
$session->start();

$session->set('foo', 'bar');
echo $session->get('foo');

// RESULT: bar

echo $session->get('unset_variable')

// RESULT: null;

echo $session->get('unset_variable2', 'default_var')

// RESULT: default_var;
```

#### Additional methods
To retrieve all the data from the session storage you can call `all()`

To clear all the data in the session storage you can call `clear()`

To remove a piece of data in the session storage you can call `remove()` with a parmeter of the name of the variable you wish to remove. If that variable is set then it's value will be returned. If the variable is not set then null will be returned.

To check if a piece of data is present in the session storage you can call `has()` with a parameter of the variable you wish to check. This returns a boolean depending on if the data is set.

You can iterate over the data in session storage by calling `getIterator()`. This will create an [ArrayIterator](https://secure.php.net/manual/en/class.arrayiterator.php) object containing all the data in the session storage object.

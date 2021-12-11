## Overview

The Event package provides foundations to build event systems and an implementation supporting prioritized listeners.

### Events

#### Example

An event has a name and can transport arguments.

```php
<?php
use Joomla\Event\Event;

// Creating an Event called "onSomething".
$event = new Event('onSomething');

// Adding an argument named "foo" with value "bar".
$event->addArgument('foo', 'bar');

// Setting the "foo" argument with a new value.
$event->setArgument('foo', new \stdClass);

// Getting the "foo" argument value.
$foo = $event->getArgument('foo');
```

Its propagation can be stopped

```php
$event->stopPropagation();
```

### Event Listeners

An event listener is a any valid [callable](https://www.php.net/manual/en/language.types.callable.php) that can be executed by the event dispatcher.

** The event listener MUST accept a `Joomla\Event\EventInterface` instance as its lone parameter. **

```php
<?php
use Joomla\Event\EventInterface;

$listener = function (EventInterface $event)
{
	// Do something with the event, you might want to inspect its arguments.
};
```

### Event Subscribers

An event subscriber can listen to one or more Events. A subscriber is any PHP class implementing `Joomla\Event\SubscriberInterface`.

** The methods designated as listeners MUST accept a `Joomla\Event\EventInterface` instance as their lone parameter. **

The subscriber listens to events as configured by the `getSubscribedEvents()` method.

```php
<?php
namespace App;

use Joomla\Event\EventInterface;
use Joomla\Event\SubscriberInterface;

/**
 * A subscriber listening to content manipulation events.
 */
class ContentSubscriber implements SubscriberInterface
{
	/**
	 * Returns an array of events this subscriber will listen to.
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onAfterContentSave'  => 'afterContentSave',
			'onBeforeContentSave' => 'beforeContentSave',
		];
	}

	/**
	 * Listens to the onBeforeContentSave event.
	 */
	public function beforeContentSave(EventInterface $event)
	{
		// Do something with the event, you might want to inspect its arguments.
	}

	/**
	 * Listens to the onAfterContentSave event.
	 */
	public function afterContentSave(EventInterface $event)
	{
		// Do something with the event, you might want to inspect its arguments.
	}
}
```

### The Dispatcher

The Dispatcher is the central point of the event system, it manages the registration of listeners and dispatching of events.

#### Registering Event Listeners

As an event listener can be any valid callable, almost anything can be registered as an event listener.

```php
<?php
namespace App;

use Joomla\Event\Dispatcher;
use Joomla\Event\EventInterface;
use Joomla\Event\Priority;

function handle_event(EventInterface $event)
{
	// Do something with the event, you might want to inspect its arguments.
}

// This is the subscriber class from the above example
$subscriber = new ContentSubscriber;

$dispatcher = new Dispatcher;

// Registering a method from a class instance
$dispatcher->addListener(
	'onAfterContentSave',
	[$subscriber, 'afterContentSave']
);

// Registering a function, you can also customise the priority to indicate if a listener should run sooner or later
$dispatcher->addListener(
	'onAfterContentSave',
	'App\handle_event',
	Priority::HIGH
);

// Registering a Closure
$dispatcher->addListener(
	'onAfterContentSave',
	function (EventInterface $event)
	{
		// Do something with the event, you might want to inspect its arguments.
	}
);
```

#### Registering Event Subscribers

Following the example above, you can register the `ContentSubscriber` to the dispatcher:

```php
<?php
namespace App;

use Joomla\Event\Dispatcher;

// Creating a dispatcher.
$dispatcher = new Dispatcher;

$dispatcher->addSubscriber(new ContentSubscriber);
```

#### Registration with Priority

```php
<?php
namespace App;

use Joomla\Event\Dispatcher;
use Joomla\Event\Priority;

// This is the subscriber class from the above example
$subscriber = new ContentSubscriber;

$dispatcher = new Dispatcher;

$dispatcher->addListener(
	'onBeforeContentSave',
	[$subscriber, 'beforeContentSave'],
	Priority::HIGH
);

$dispatcher->addListener(
	'onAfterContentSave',
	[$subscriber, 'afterContentSave'],
	Priority::ABOVE_NORMAL
);
```

The default priority is `Joomla\Event\Priority::NORMAL`.

If multiple listeners have the same priority for a given event, they will be called in the order they were added to the Dispatcher.

#### Removing Event Listeners

Sometimes, you may need to remove a listener from the dispatcher; in this case the `removeListener()` method should be used.

```php
<?php
namespace App;

use Joomla\Event\Dispatcher;
use Joomla\Event\Priority;

// This is the subscriber class from the above example
$subscriber = new ContentSubscriber;

$dispatcher = new Dispatcher;

$dispatcher->addListener(
	'onBeforeContentSave',
	[$subscriber, 'beforeContentSave'],
	Priority::HIGH
);

$dispatcher->addListener(
	'onAfterContentSave',
	[$subscriber, 'afterContentSave'],
	Priority::ABOVE_NORMAL
);

// Changed our mind, we don't want the onAfterContentSave event processed
$dispatcher->removeListener(
	'onAfterContentSave',
	[$subscriber, 'afterContentSave']
);
```

#### Removing Event Subscribers

Similar to listeners, we may also remove event subscribers from the dispatcher.

```php
<?php
namespace App;

use Joomla\Event\Dispatcher;

$subscriber = new ContentSubscriber;

// Creating a dispatcher.
$dispatcher = new Dispatcher;

$dispatcher->addSubscriber(new ContentSubscriber);
$dispatcher->removeSubscriber(new ContentSubscriber);
```

#### Registering Custom Events

** NOTE: This functionality is deprecated and will be removed in 3.0 **

You can register Events to the Dispatcher, if you need custom default event objects.

```php
<?php
namespace App;

use Joomla\Event\Dispatcher;
use Joomla\Event\Event;

// Creating an event with a "foo" argument.
$event = new Event('onBeforeContentSave');
$event->setArgument('foo', 'bar');

// Registering the event to the Dispatcher.
$dispatcher = new Dispatcher;
$dispatcher->addEvent($event);
```

By default, an `Event` object is created with no arguments, when triggering the Event.

#### Dispatching Events

Once you registered your listeners (and eventually events to the Dispatcher), you can dispatch the events.

The listeners will be called in a queue according to their priority for that Event.

```php
<?php
// Triggering the onAfterSomething Event.
$dispatcher->dispatch('onAfterSomething');
```

If you registered an Event object having the `onAfterSomething` name, then it will be passed to all listeners instead of the default one.

You can also pass a custom Event when triggering it

```php
<?php
namespace App;

use Joomla\Event\Dispatcher;
use Joomla\Event\Event;

// Creating an event called "onAfterSomething" with a "foo" argument.
$event = new Event('onAfterSomething');
$event->setArgument('foo', 'bar');

$dispatcher = new Dispatcher;

// Triggering the onAfterSomething Event.
$dispatcher->dispatch('onAfterSomething', $event);
```

### Stopping the Propagation

As noted above, you can stop the Event propagation; this will cause the dispatcher to stop calling event listeners.

```php
<?php
namespace App;

use Joomla\Event\Event;

class ContentListener
{
	public function onBeforeContentSave(Event $event)
	{
		// Stopping the Event propagation.
		$event->stopPropagation();
	}
}
```

### Observable classes

Observable classes depend on a Dispatcher, and they may implement the `DispatcherAwareInterface` interface.

The `DispatcherAwareTrait` is provided to make implementing this interface as easy as possible.

Example of a Model class:

```php
<?php
namespace App;

use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherAwareTrait;

class ContentModel implements DispatcherAwareInterface
{
	use DispatcherAwareTrait;

	private const ON_BEFORE_SAVE_EVENT = 'onBeforeSaveEvent';
	private const ON_AFTER_SAVE_EVENT = 'onAfterSaveEvent';

	public function save()
	{
		$this->dispatcher->dispatch(self::ON_BEFORE_SAVE_EVENT);

		// Perform the saving.

		$this->dispatcher->dispatch(self::ON_AFTER_SAVE_EVENT);
	}
}
```

### Immutable Events

An immutable event cannot be modified after its instanciation.

It is useful when you don't want the listeners to manipulate it (they can only inspect it).

```php
<?php
namespace App;

use Joomla\Event\EventImmutable;

// Creating an immutable event called onSomething with an argument "foo" with value "bar"
$event = new EventImmutable('onSomething', array('foo' => 'bar'));
```

### Lazy event listeners

Sometimes, you may have an event listener class which cannot be instantiated before the listener is registered with the Dispatcher (i.e. a circular dependency in your application). For scenarios such as this, the `Joomla\Event\LazyServiceEventListener` class is available which serves as a decorator around this service and loads it from a [PSR-11](http://www.php-fig.org/psr/psr-11/) compatible container.

**NOTE** This feature is only available for single event listeners, it cannot be used to lazily load a subscriber implementing `Joomla\Event\SubscriberInterface`.

```php
<?php
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\EventInterface;
use Joomla\Event\LazyServiceEventListener;

// This can be any PSR-11 compatible container
$container = new Container;
$container->set(
	'lazy.service.listener',
	function ()
	{
		// Instantiate your complex service, for brevity we will create a simple class which can be invoked
		return new class
		{
			public function __invoke(EventInterface $event)
			{
				// Handle the event
			}
		};
	}
);
$container->set(
	'lazy.service.listener_with_method_name',
	function ()
	{
		// Instantiate your complex service, for brevity we will create a simple class
		return new class
		{
			public function onSomeEvent(EventInterface $event)
			{
				// Handle the event
			}
		};
	}
);

$dispatcher = new Dispatcher;

// The lazy listener can be created without specifying a method to be called if the class has an `__invoke()` method
$dispatcher->addListener('some.event', new LazyServiceEventListener($container, 'lazy.service.listener'));

// Or, the lazy listener can be used to trigger a specific method on a class
$dispatcher->addListener('some.event', new LazyServiceEventListener($container, 'lazy.service.listener_with_method_name', 'onSomeEvent'));
```

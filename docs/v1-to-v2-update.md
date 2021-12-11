## Updating from v1 to v2

### Minimum supported PHP version raised

All Framework packages now require PHP 7.2 or newer.

### Interface Changes

This section describes all changes made to the interfaces of this package.

#### `DispatcherInterface` changes

The following changes have been made to `Joomla\Event\DispatcherInterface` and directly impact `Joomla\Event\Dispatcher`.

##### `triggerEvent()` removed

The `triggerEvent()` method has been removed from the interface and is no longer a requirement for dispatchers to implement.

The method is still present in `Joomla\Event\Dispatcher` but deprecated and will be removed in 3.0, the `dispatch()` method should be used instead.

##### `dispatch()` added

A new `dispatch()` method has been added as a replacement for the `triggerEvent()` method. Unlike the now deprecated `triggerEvent()` method, the new `dispatch()` method requires the event name and `EventInterface` object as separate arguments.

##### Listener management methods added

The `addListener()`, `clearListeners()`, `countListeners()`, `getListeners()`, `hasListener()`, and `removeListener()` methods from `Joomla\Event\Dispatcher` have been added to the interface, all dispatchers are now required to implement these methods.

##### Subscriber management methods added

The new version of the Event package introduces the concept of an event subscriber, the interface now requires an `addSubscriber()` and `removeSubscriber()` method to manage subscribers.

#### `EventInterface` changes

The following changes have been made to `Joomla\Event\EventInterface` and directly impact `Joomla\Event\AbstractEvent` and its subclasses.

##### `getArgument()` added

The `getArgument()` method has been added to the interface, all event objects must now expose an API for reading arguments.

##### `stopPropagation()` added

The `stopPropagation()` method has been added to the interface, this method replaces the now deprecated `Joomla\Event\Event::stop()` method. All event objects (including immutable events based on `Joomla\Event\EventImmutable`) must now support receiving a signal to stop propagating the event to listeners.

### Class Changes

In addition to changes in the interfaces, the classes in this package also contain changes to be aware of.

#### `DelegatingDispatcher` removed

The `Joomla\Event\DelegatingDispatcher` class has been removed, create your own delegating (decorating) dispatcher as needed.

#### `Dispatcher` Changes

The following changes have been made to `Joomla\Event\Dispatcher`.

##### Listener filtering removed

The deprecated listener filtering logic has been removed. The methods which deal with listeners now consider a listener to be any valid callable, and an event subscriber must explicitly declare the events it subscribes to and the methods within the class that handle those events.

##### Default event object support deprecated

Support for creating default event objects and storing them in the dispatcher has been deprecated and will be removed in 3.0. This impacts the `addEvent()`, `clearEvents()`, `countEvents()` `getEvent()`, `getEvents()`, `hasEvent()`, `removeEvent()`, and `setEvent()` methods.

Additionally, as of 3.0, the `$event` argument of the `dispatch()` method will no longer be optional as a result of this change.

##### Changes from re-classification of event listeners

In v1 of the Event package, a listener was considered any PHP class and all of its public methods would be considered potential callbacks for events with a similar method name (i.e. if your listener had an `onBeforeExecute()` method, it would be called if your application emitted an `onBeforeExecute` event).

In v2, a listener is now a single callable function for a single event, and event names are no longer required to be valid PHP class method names.

To continue to use a PHP class as a collection of event listeners, your class should now implement `Joomla\Event\SubscriberInterface` and the class registered with the `addSubscriber()` method.

###### `addListener()` signature changed

The method now requires an event name and a callback as compulsory arguments with an optional argument to specify the callback's priority.

###### `getListenerPriority()` signature changed

The method now requires the event name and a callback as its requirements to determine the priority for a listener of an event.

###### `getListeners()` signature changed

The method no longer requires an event to be provided which will allow a caller to retrieve all listeners for all events.

The lone `$event` argument no longer accepts an `EventInterface` object, it MUST receive a string containing the event name.

###### `hasListener()` signature changed

The method now typehints its first argument as a `callable`, and the `$event` argument no longer accepts an `EventInterface` object.

###### `removeListener()` signature changed

The method now requires an event name and a callback as compulsory arguments.

#### `Event` Changes

The following changes have been made to `Joomla\Event\Event`.

##### `stop()` deprecated

The `stop()` method, only implemented on `Joomla\Event\Event`, has been deprecated. The `stopPropagation()` method, as now required by the interface and implemented in `Joomla\Event\AbstractEvent`, should be used instead.

#### `ListenersPriorityQueue` Changes

The `Joomla\Event\ListenersPriorityQueue` class has been made final and marked as internal, this is an internal class used for managing the event listener priorities for each event and not intended to be a supported public API.

#### `Priority` Changes

A private constructor has been added to the `Joomla\Event\Priority` class; this is an enum class and not intended to be instantiated or used.

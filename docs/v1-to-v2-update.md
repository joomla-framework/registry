## Updating from v1 to v2

The following changes were made to the Session package between v1 and v2.

### Minimum supported PHP version raised

All Framework packages now require PHP 7.2 or newer.

### Joomla\Session\Storage class chain removed

The `Storage` class chain as it existed in v1 has been removed and in v2 is replaced with a `StorageInterface` and `HandlerInterface` (an extension of PHP's `SessionHandlerInterface`) set of classes to separate architectural concerns and improve use of the package.

### Joomla\Session\Session - Underscore prefixed methods removed

In v1, there are several protected methods named with a leading underscore. These are renamed without the underscore to comply with the Joomla! Coding Standard.

### Joomla\Session\SessionInterface added

A new interface, `Joomla\Session\SessionInterface` has been added to represent the primary session API.

### Joomla\Session\HandlerInterface added

A new interface, [Joomla\Session\HandlerInterface](classes/HandlerInterface.md), has been added as an extension of PHP's native `SessionHandlerInterface`. Classes implementing this interface are largely what the `Joomla\Session\Storage` class chain in v1 was handling.

### Joomla\Session\StorageInterface added

A new interface, [Joomla\Session\StorageInterface](classes/StorageInterface.md), has been added to represent a class acting as a session store. Abstracting this logic to a new interface improves the internal architecture of the package and enables better testing of the API. There are two default implementations:

1) `Joomla\Session\Storage\NativeStorage`, which stores data to PHP's `$_SESSION` superglobal.
2) `Joomla\Session\Storage\RuntimeStorage` which stores data in PHP's memory. This is useful for when running unit tests or command-line applications.

### Joomla\Session\Session::getInstance() removed

The base `Joomla\Session\Session` class no longer supports singleton object storage. The `Session` object should be stored in your application's DI container or the `Joomla\Application\AbstractWebApplication` object.

### Session::initialise() removed

The `initialise` method has been removed. The base `Session` class now requires a `Joomla\Input\Input` object as part of the constructor and the `Joomla\Event\DispatcherInterface` object should be injected via the constructor or the `setDispatcher` method.

### Namespaced session variables dropped

Support for namespaced session variables has been removed from the `Session` API. Previously, data was stored to the `$_SESSION` global in a top level container, such as `$_SESSION[$namespace]`. In v2, data is stored directly to the global.

### Event dispatching modified

In v1, when the `onAfterSessionStart` method was dispatched, a generic `Joomla\Event\Event` object was passed with no parameters. In v2, a `Joomla\Session\SessionEvent` object has been added and is dispatched with the `onAfterSessionStart` and new `onAfterSessionRestart` events. The `SessionEvent` object is dispatched with the current `Joomla\Session\SessionInterface` instance attached so its API is accessible within the events.

### Session Validation Abstracted

In order to allow the session package to be more easily integrated with other providers we have abstracted the session validators. A new interface, [`Joomla\Session\ValidatorInterface`](classes/ValidatorInterface.md) has been added to make it easier to add custom session validators as well as removing the dependency of the `joomla/input` package from the session package. This makes it easier when integrating the session package with PSR-7 compliant applications.

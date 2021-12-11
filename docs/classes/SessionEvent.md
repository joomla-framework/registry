## Joomla\Session\SessionEvent

The `SessionEvent` class is an event class that is the root of all session event classes for [`Joomla\Session\SessionInterface`](SessionInterface.md) implementations which support dispatching events.

### Get the current session

The `getSession()` method is used to get the current `SessionInterface` which dispatched the event.

```php
/**
 * @return  SessionInterface
 */
public function getSession(): SessionInterface
```

## Joomla\Session\Session

The `Session` class is the default implementation of [`Joomla\Session\SessionInterface`](SessionInterface.md). In addition to implementing the interface, the `Session` class provides additional features such as lazily started sessions, validation of session data, and event hooks for allowing integrations to act on key events in the session lifetime.

### Session Data Validation

#### Add a session validator

The `addValidator()` method is used to add a validator for the current session, triggered immediately after the session is started. All validators must implement [`Joomla\Session\ValidatorInterface`](ValidatorInterface.md).

```php
/**
 * @param   ValidatorInterface  $validator  The session validator
 *
 * @return  void
 */
public function addValidator(ValidatorInterface $validator);
```

### Session State Interactions

#### Get the session state

The `getState()` method is used to get the current state of the session.

```php
/**
 * Get current state of session
 *
 * @return  string  The session state
 */
public function getState();
```

### Event Hooks

#### Session Started

When a session is started, the `Session` class dispatches the `SessionEvents::START` event to allow event listeners to perform extra actions after a session has been started. A [`Joomla\Session\SessionEvent`](SessionEvent.md) instance is provided to listeners with the current `Session` instance.

Note, for backward compatibility the `onAfterSessionStart` event is also dispatched if any listeners are subscribed to that event, however this event is deprecated and will not be supported in the Joomla! Framework 3.0 release.

#### Session Restarted

When a session is restarted, the `Session` class dispatches the `SessionEvents::RESTART` event to allow event listeners to perform extra actions after a session has been restarted. A [`Joomla\Session\SessionEvent`](SessionEvent.md) instance is provided to listeners with the current `Session` instance.

Note, for backward compatibility the `onAfterSessionRestart` event is also dispatched if any listeners are subscribed to that event, however this event is deprecated and will not be supported in the Joomla! Framework 3.0 release.

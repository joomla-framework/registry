## Joomla\Session\SessionInterface

The `SessionInterface` defines an class which manages session data in a web application. This interface is an extension of PHP's [`IteratorAggregate`](https://www.php.net/manual/en/class.iteratoraggregate.php) interface and implementations must comply with the iterator's requirements as well.

Functionally, this interface is a wrapper around [`Joomla\Session\StorageInterface`](StorageInterface.md) and many of the methods are inherently similar, please see the documentation for the `StorageInterface` for more details about the noted methods.

### Storage Wrapping Methods

* `getName()`
* `setName()`
* `getId()`
* `getId()`
* `isActive()`
* `isStarted()`
* `get()`
* `set()`
* `has()`
* `remove()`
* `clear()`
* `all()`
* `close()`
* `gc()`
* `abort()`

### Get the session lifetime

The `getExpire()` method is used to get the session lifetime in seconds.

```php
/**
 * @return  integer  The session expiration time in seconds
 */
public function getExpire();
```

### Check if session is new

The `is()` method is used to determine if a session is new (generally, this is true if the session was created in the current request).

```php
/**
 * @return  boolean
 */
public function isNew();
```

### Get the session token

The `getToken()` method is used to fetch the current session token. This token is generally used in conjunction with CSRF related checks.

```php
/**
 * @param   boolean  $forceNew  If true, forces a new token to be created
 *
 * @return  string
 *
 * @since   __DEPLOY_VERSION__
 */
public function getToken($forceNew = false);
```

### Check if the session has a token

The `hasToken()` method is used to determine if the current session token matches the specified token.

```php
/**
 * Check if the session has the given token.
 *
 * @param   string   $token        Hashed token to be verified
 * @param   boolean  $forceExpire  If true, expires the session
 *
 * @return  boolean
 *
 * @since   __DEPLOY_VERSION__
 */
public function hasToken($token, $forceExpire = true);
```

### Start the session

The `start()` method is used to start the session. Generally, this includes calling the `StorageInterface::start()` method, validating the current session against potential attacks, configuring any session related internal behaviors, and optionally dispatching events to subscribers.

```php
/**
 * @return  void
 */
public function start();
```

### Destroy the session

The `start()` method is used to start the session. Generally, this includes calling the `StorageInterface::start()` method, validating the current session against potential attacks, configuring any session related internal behaviors, and optionally dispatching events to subscribers.

```php
/**
 * @return  boolean
 */
public function destroy();
```

### Restart the session

The `restart()` method is used to restart an expired or locked session. Generally, this includes calling the `StorageInterface::start()` method, validating the current session against potential attacks, configuring any session related internal behaviors, and optionally dispatching events to subscribers.

```php
/**
 * @return  void
 */
public function restart();
```

### Fork the session

The `fork()` method is used to generate a new session with the existing data.

An example use of this method is to migrate the session data to a new session (with a new ID) after a user authenticates with your application.

```php
/**
 * @return  boolean
 */
public function fork();
```

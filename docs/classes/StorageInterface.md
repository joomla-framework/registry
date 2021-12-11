## Joomla\Session\StorageInterface

The `StorageInterface` defines an class which represents a data store for session data.

### Session Data Interactions

#### Get the session name

The `getName()` method is used to read the session name.

```php
/*
 * @return  string  The session name
 */
public function getName(): string;
```

#### Set the session name

The `setName()` method is used to set the session name. In regular use, the `Joomla\Session\Session` object will inject the calculated session name into the storage instance.

```php
/*
 * @param   string  $name  The session name
 *
 * @return  $this
 */
public function setName(string $name);
```

#### Get the session ID

The `getId()` method is used to read the session ID.

```php
/*
 * @return  string  The session ID
 */
public function getId(): string;
```

#### Set the session ID

The `setId()` method is used to set the session ID. In regular use, the `Joomla\Session\Session` object will inject the calculated session ID into the storage instance.

```php
/*
 * @param   string  $name  The session ID
 *
 * @return  $this
 */
public function setId(string $name);
```

### Session State Interactions

#### Check for an active session

The `isActive()` method is used to check if there is an active session for the current request.

```php
/*
 * @return  boolean
 */
public function isActive(): bool;
```

#### Check if a session has been started

The `isStarted()` method is used to check if the active session for the current request has been started.

```php
/*
 * @return  boolean
 */
public function isStarted(): bool;
```

#### Start the session

The `start()` method is used to start a session if it has not already been started.

```php
/**
 * @return  void
 */
public function start();
```

#### Regenerate the session ID

The `regenerate()` method is used to regenerate the current session ID.

Note, for implementations that interface with PHP's session extension, the [`session_regenerate_id()`](https://www.php.net/manual/en/function.session-regenerate-id.php) function must be called by this method.

```php
/**
 * @param   boolean  $destroy  Destroy session when regenerating?
 *
 * @return  boolean  True on success
 */
public function regenerate(bool $destroy = false): bool;
```

#### Close the session

The `close()` method is used to write the data to storage and close the current session.

```php
/**
 * @return  void
 */
public function close();
```

#### Perform session garbage collection

The `gc()` method is used to trigger the garbage collection process for the backend storage.

```php
/**
 * @return  integer|boolean  Number of deleted sessions on success or boolean false on failure or if the function is unsupported
 */
public function gc();
```

#### Abort the session

The `abort()` method is used to abort the current session.

```php
/**
 * @return  boolean
 */
public function abort();
```

### Data Store Interactions

#### Read from the data store

The `get()` method is used to read the data for a given key from the data store, returning its current value or the given default value if the requested key was not found.

```php
/*
 * @param   string  $name     Name of a variable
 * @param   mixed   $default  Default value of a variable if not set
 *
 * @return  mixed  Value of a variable
 */
public function get(string $name, $default);
```

#### Write to the data store

The `set()` method is used to write data for a given key to the data store, returning its previous value if one was set.

```php
/*
 * @param   string  $name   Name of a variable
 * @param   mixed   $value  Value of a variable
 *
 * @return  mixed  Old value of a variable
 */
public function set(string $name, $value);
```

#### Check if value is defined in data store

The `has()` method is used to check if a given key has been set in the data store.

```php
/*
 * @param   string  $name  Name of variable
 *
 * @return  boolean
 */
public function has(string $name): bool;
```

#### Remove key from the data store

The `remove()` method is used to remove a given key from the data store, returning its current value if set.

```php
/*
 * @param   string  $name  Name of variable
 *
 * @return  mixed   The value from session or NULL if not set
 */
public function remove(string $name);
```

#### Empty the data store

The `clear()` method is used to empty the data store.

```php
/*
 * @return  void
 */
public function clear();
```

#### Retrieve all data from the data store

The `all()` method is used to fetch all data from the data store.

```php
/*
 * @return  array
 */
public function all(): array;
```

## Joomla\Session\HandlerInterface

The `HandlerInterface` is an extension of the native [`SessionHandlerInterface`](https://secure.php.net/manual/en/class.sessionhandlerinterface.php) adding additional capabilities to session handlers.

Implementations of the `SessionHandlerInterface` are used by a `Joomla\Session\Storage\NativeStorage` instance for managing session data. Note that the Session API does not require session handlers to implement the `Joomla\Session\HandlerInterface`, all uses typehint against the `SessionHandlerInterface` and check whether the extended interface is used.

### Check if the handler is supported

The `isSupported` method is used to validate whether a handler can be used in the current environment. Handlers implementing this interface should add internal checks to ensure its dependencies are available. For example, the `Joomla\Session\Handler\MemcachedHandler::isSupported()` method ensures that Memcached is available on the server.

```php
/*
 * @return  boolean
 */
public static function isSupported();
```

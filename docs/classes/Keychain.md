## Joomla\Keychain\Keychain

The `Keychain` class is an extension of the `Joomla\Registry\Registry` class which provides a way to securely store sensitive information such as access credentials or any other data.

### Creating your Keychain

The `Keychain` class constructor takes 1 compulsory and 1 optional parameter:

```php
/**
 * Constructor
 *
 * @param   Crypt  $crypt  The encryption handler.
 * @param   mixed  $data   The data to bind to the new Keychain.
 */
public function __construct(Crypt $crypt, $data = null)
```

Please review the documentation for the [Crypt](https://github.com/joomla-framework/crypt) package for information about configuring a `Joomla\Crypt\Crypt` instance.

Once created, the `Keychain` class inherits the `Joomla\Registry\Registry` class' API with added methods for loading a file previously saved by the `Keychain` class and saving the data from a `Keychain` as an encrypted file.

### Loading an encrypted file

To load a previously saved keychain, the `loadKeychain()` method should be called, specifying the path to the file to be loaded. A `\RuntimeException` is thrown if the specified file does not exist. The `Keychain` class does not catch exceptions thrown by the `Crypt`, therefore it is suggested to implement Exception handling for any `Joomla\Crypt\Exception\CryptExceptionInterface` objects.

```php
/**
 * Load a keychain file into this object.
 *
 * @param   string  $keychainFile  Path to the keychain file.
 *
 * @return  $this
 *
 * @throws  \Joomla\Crypt\Exception\CryptExceptionInterface if the keychain cannot be decrypted
 * @throws  \RuntimeException if the keychain file does not exist
 */
public function loadKeychain($keychainFile)
```

### Saving an encrypted file

To save the data from your keychain, the `saveKeychain()` method should be called, specifying the path for the file to be created. A `\RuntimeException` is thrown if the specified file path is empty. The `Keychain` class does not catch exceptions thrown by the `Crypt`, therefore it is suggested to implement Exception handling for any `Joomla\Crypt\Exception\CryptExceptionInterface` objects.

```php
/**
 * Save this keychain to a file.
 *
 * @param   string  $keychainFile  The path to the keychain file.
 *
 * @return  boolean  Result of storing the file.
 *
 * @throws  \Joomla\Crypt\Exception\CryptExceptionInterface if the keychain cannot be encrypted
 * @throws  \RuntimeException if the keychain file path is invalid
 */
public function saveKeychain($keychainFile)
```

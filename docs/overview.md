## Overview

The Keychain package is an extension of the [Registry](https://github.com/joomla-framework/registry) package which provides a way to securely store sensitive information such as access credentials or any other data.

### Creating your Keychain

The `Joomla\Keychain\Keychain` class constructor takes 1 compulsory and 1 optional parameter:

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

To load a previously saved keychain, the `loadKeychain()` method should be called, specifying the path to the file to be loaded.

```php
<?php
use Joomla\Crypt\Crypt;
use Joomla\Keychain\Keychain;

// You should configure your own `Joomla\Crypt\CipherInterface` and `Joomla\Crypt\Key` objects for proper re-use, however this example will create a valid `Joomla\Crypt\Crypt` instance
$crypt = new Crypt;

$keychain = new Keychain($crypt);
$keychain->loadKeychain('/path/to/keychain.file');
```

### Saving an encrypted file

To save the data from your keychain, the `saveKeychain()` method should be called, specifying the path for the file to be created.

```php
<?php
use Joomla\Crypt\Crypt;
use Joomla\Keychain\Keychain;

// You should configure your own `Joomla\Crypt\CipherInterface` and `Joomla\Crypt\Key` objects for proper re-use, however this example will create a valid `Joomla\Crypt\Crypt` instance
$crypt = new Crypt;

$keychain = new Keychain($crypt);
$keychain->set('foo', 'bar');
$keychain->saveKeychain('/path/to/keychain.file');
```

### Command line management

A keychain can optionally be managed if your application integrates the [Console](https://github.com/joomla-framework/console) package, please review the [command line management](features/command-line-management.md) page for more information.

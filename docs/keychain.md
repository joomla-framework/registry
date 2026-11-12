# Keychain

`Joomla\Registry\Keychain` extends `Registry` with encryption, so the same dot-notation store can
hold access credentials or anything else that must not sit on disk in clear text.

It is an **optional feature**: nothing in the rest of the package touches it, and the class is only
usable once `joomla/crypt` is installed. The examples below use the `Crypto` cipher, which also
needs `defuse/php-encryption`:

```bash
composer require joomla/crypt defuse/php-encryption
```

> This class was part of the separate `joomla/keychain` package until registry 4.1. See
> [Coming from joomla/keychain](#coming-from-joomlakeychain) below.

## Creating a keychain

```php
public function __construct(Crypt $crypt, $data = null)
```

The second argument binds initial data, exactly as `Registry` does. The first is a `Crypt` instance
configured with a cipher **and a key you keep**: a keychain can only be read back with the key it
was saved with.

Generate the key once and store it outside the keychain — in an environment variable, a secrets
manager or a file outside the web root:

```php
use Joomla\Crypt\Cipher\Crypto;

$key = (new Crypto())->generateKey();

// Store this string somewhere safe; it is the only way to decrypt the keychain.
echo $key->getPrivate();
```

Then build the `Crypt` from the stored key every time:

```php
use Joomla\Crypt\Cipher\Crypto;
use Joomla\Crypt\Crypt;
use Joomla\Crypt\Key;
use Joomla\Registry\Keychain;

$crypt    = new Crypt(new Crypto(), new Key('crypto', getenv('KEYCHAIN_KEY'), ''));
$keychain = new Keychain($crypt);
```

Do not use `new Crypt()` without arguments. In joomla/crypt 4.x it falls back to the `Crypto` cipher
with a freshly generated key that is never stored, so a keychain saved that way cannot be opened
again by the next request. joomla/crypt 5.0 requires both arguments.

`Crypto` authenticates its ciphertext: a wrong key or a modified file raises
`Joomla\Crypt\Exception\DecryptionException`. Prefer it over the other ciphers in joomla/crypt.

## Loading and saving

```php
$keychain->loadKeychain('/path/to/keychain.dat');

$keychain->set('database.password', $secret);

$keychain->saveKeychain('/path/to/keychain.dat');
```

| Method | Behaviour |
|---|---|
| `loadKeychain($file)` | Decrypts the file and binds it. Returns `$this`. Raises `\RuntimeException` if the file does not exist |
| `saveKeychain($file)` | Encrypts the data and writes it. Returns a boolean. Raises `\RuntimeException` if the path is empty |

Neither method catches the exceptions `Crypt` raises, so handle
`Joomla\Crypt\Exception\CryptExceptionInterface` yourself:

```php
use Joomla\Crypt\Exception\CryptExceptionInterface;

try {
    $keychain->loadKeychain($file);
} catch (CryptExceptionInterface $e) {
    // Wrong key, or the file is not what it claims to be
}
```

Everything else — `get()`, `set()`, `exists()`, `def()`, array access, iteration — comes from
`Registry` unchanged.

## Managing a keychain from the command line

Five console commands ship with the package. They need
[`joomla/console`](https://github.com/joomla-framework/console), which is likewise optional:

```bash
composer require joomla/console
```

Each command takes a `Crypt` instance configured as shown above:

```php
use Joomla\Console\Application;
use Joomla\Crypt\Cipher\Crypto;
use Joomla\Crypt\Crypt;
use Joomla\Crypt\Key;
use Joomla\Registry\Command\AddEntryCommand;
use Joomla\Registry\Command\DeleteEntryCommand;
use Joomla\Registry\Command\EditEntryCommand;
use Joomla\Registry\Command\ListEntriesCommand;
use Joomla\Registry\Command\ReadEntryCommand;

$crypt = new Crypt(new Crypto(), new Key('crypto', getenv('KEYCHAIN_KEY'), ''));

$app = new Application();
$app->addCommand(new AddEntryCommand($crypt));
$app->addCommand(new DeleteEntryCommand($crypt));
$app->addCommand(new EditEntryCommand($crypt));
$app->addCommand(new ListEntriesCommand($crypt));
$app->addCommand(new ReadEntryCommand($crypt));
$app->execute();
```

| Command | Arguments | Purpose |
|---|---|---|
| `keychain:add-entry` | `<filename> <entry-name> <entry-value>` | Adds an entry, refusing to overwrite an existing key |
| `keychain:edit-entry` | `<filename> <entry-name> <entry-value>` | Sets an entry's value; also creates the entry if it does not exist |
| `keychain:delete-entry` | `<filename> <entry-name>` | Removes an entry; a missing key is reported as a note |
| `keychain:list` | `<filename> [--print-values]` | Lists the top-level keys, with their values if `--print-values` is given |
| `keychain:read-entry` | `<filename> <entry-name>` | Prints one entry; a missing key is reported as a warning |

The file must already exist (see [below](#things-to-know-before-you-build-on-this)). `--help` on each
command describes its arguments.

## Things to know before you build on this

**The keychain file is written with default permissions.** `saveKeychain()` calls
`file_put_contents()` without adjusting the mode, so with a typical umask the file ends up
world-readable. Tighten it yourself after saving:

```php
$keychain->saveKeychain($file);
chmod($file, 0600);
```

**Writes are not atomic and not locked.** A crash mid-write leaves an unreadable keychain, and two
processes saving at once will lose one set of changes. If that matters, write to a temporary file
and `rename()` it into place.

**Load errors are not always reported.** `loadKeychain()` checks that the file exists but not that
it could be read, and does not check the result of `json_decode()`. With `Crypto` a damaged file
fails to decrypt and raises an exception. With a cipher that does not authenticate its ciphertext,
such as `OpenSSL`, a corrupted or tampered file can decrypt to noise and yield an empty keychain
rather than an error — so a missing entry can mean "not set" or "file damaged".

```php
$keychain->loadKeychain($file);

if ($keychain->get('expected-key') === null) {
    // Cannot tell a missing entry from a damaged file. Check explicitly if it matters.
}
```

**The class extends `Registry`.** That brings `loadFile()`, `loadString()`, `toString()`,
`__toString()` and `jsonSerialize()` along with it — all of which read or write **unencrypted**
data. A stray `json_encode($keychain)` or string interpolation writes every secret in clear text.
Treat the object as write-only outside of `get()`.

**Secrets on the command line are visible to other users.** `keychain:add-entry` and
`keychain:edit-entry` only accept the value as an argument, and command lines are readable through
`ps` and land in the shell history. For sensitive values, set them from PHP instead.

**Nested entries break two commands.** A key with a dot, such as `database.password`, is stored as a
nested value, as everywhere in `Registry`. Once the keychain contains one, `keychain:list
--print-values` fails with "A cell must be a TableCell, a scalar or an object implementing
`__toString()`", and so does `keychain:read-entry database`. Reading the full path,
`keychain:read-entry <file> database.password`, works.

**There is no `keychain:create`.** Every command requires the file to exist already, so the first
entry cannot be added through the CLI. Create the file from PHP once:

```php
(new Keychain($crypt))->saveKeychain('/path/to/keychain.dat');
```

## Coming from joomla/keychain

The `joomla/keychain` package was folded into `joomla/registry` in 4.1 and is no longer maintained
separately. The classes are unchanged apart from their namespace:

| Was | Is |
|---|---|
| `Joomla\Keychain\Keychain` | `Joomla\Registry\Keychain` |
| `Joomla\Keychain\Command\AbstractKeychainCommand` | `Joomla\Registry\Command\AbstractKeychainCommand` |
| `Joomla\Keychain\Command\AddEntryCommand` | `Joomla\Registry\Command\AddEntryCommand` |
| `Joomla\Keychain\Command\DeleteEntryCommand` | `Joomla\Registry\Command\DeleteEntryCommand` |
| `Joomla\Keychain\Command\EditEntryCommand` | `Joomla\Registry\Command\EditEntryCommand` |
| `Joomla\Keychain\Command\ListEntriesCommand` | `Joomla\Registry\Command\ListEntriesCommand` |
| `Joomla\Keychain\Command\ReadEntryCommand` | `Joomla\Registry\Command\ReadEntryCommand` |

To migrate:

```bash
composer remove joomla/keychain
```

```php
// Before
use Joomla\Keychain\Keychain;

// After
use Joomla\Registry\Keychain;
```

Method signatures, behaviour, the command names and the on-disk file format are all unchanged, so
an existing keychain file keeps working and no data migration is needed.

### Licence

`joomla/keychain` was released under LGPL-2.1-or-later; `joomla/registry` is GPL-2.0-or-later, and
the migrated files now carry that header. LGPL-2.1 section 3 permits the change.

In practice this codifies what was already the case rather than tightening anything: `Keychain`
extends `Joomla\Registry\Registry`, so every consumer of the keychain was already using GPL code
from this package.

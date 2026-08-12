# Keychain

`Joomla\Registry\Keychain` extends `Registry` with encryption, so the same dot-notation store can
hold access credentials or anything else that must not sit on disk in clear text.

It is an **optional feature**: nothing in the rest of the package touches it, and the class is only
usable once `joomla/crypt` is installed.

```bash
composer require joomla/crypt
```

> This class was part of the separate `joomla/keychain` package until registry 4.1. See
> [Coming from joomla/keychain](#coming-from-joomlakeychain) below.

## Creating a keychain

```php
use Joomla\Crypt\Crypt;
use Joomla\Registry\Keychain;

$crypt    = new Crypt();
$keychain = new Keychain($crypt);
```

```php
public function __construct(Crypt $crypt, $data = null)
```

The second argument binds initial data, exactly as `Registry` does. See the
[crypt package](https://github.com/joomla-framework/crypt) for how to configure a `Crypt` with the
cipher and key you want — the default is not something to ship without thinking about.

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

Each command takes a configured `Crypt` instance:

```php
use Joomla\Console\Application;
use Joomla\Crypt\Crypt;
use Joomla\Registry\Command\AddEntryCommand;
use Joomla\Registry\Command\DeleteEntryCommand;
use Joomla\Registry\Command\EditEntryCommand;
use Joomla\Registry\Command\ListEntriesCommand;
use Joomla\Registry\Command\ReadEntryCommand;

$crypt = new Crypt();

$app = new Application();
$app->addCommand(new AddEntryCommand($crypt));
$app->addCommand(new DeleteEntryCommand($crypt));
$app->addCommand(new EditEntryCommand($crypt));
$app->addCommand(new ListEntriesCommand($crypt));
$app->addCommand(new ReadEntryCommand($crypt));
$app->execute();
```

| Command | Purpose |
|---|---|
| `keychain:add-entry` | Adds an entry, refusing to overwrite an existing key |
| `keychain:edit-entry` | Changes an existing entry |
| `keychain:delete-entry` | Removes an entry |
| `keychain:list` | Lists every key |
| `keychain:read-entry` | Prints one value |

Every command takes the path to the keychain file as its first argument. `--help` on each one lists
the rest.

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

**Load errors are not reported.** `loadKeychain()` checks that the file exists but not that it
could be read, and does not check the result of `json_decode()`. With a cipher that does not
authenticate its ciphertext, a corrupted or tampered file decrypts to noise and yields an empty
keychain rather than an error — so a missing entry can mean "not set" or "file damaged".

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
`keychain:edit-entry` take the value as an argument, and command lines are readable through `ps`
and land in the shell history. Prefer piping the value in, or set it from PHP.

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

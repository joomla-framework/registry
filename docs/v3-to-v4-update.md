# Updating from v3 to v4

Release 4.0.0 raises the PHP requirement and removes the backwards-compatibility layers that were
deprecated in 2.0 and 3.0: the `$separator` parameters and the magic accessors that kept the
`$separator` property public, and the untyped `stringToObject()` signature.

## At a glance

| | v3 (3.0.1) | v4 (4.0.0) |
|---|---|---|
| PHP | `^8.1.0` | `^8.3.0` |
| `$registry->separator` read/write | deprecated, works | **`Error`** (protected property) |
| `set()` / `loadArray()` `$separator` argument | deprecated, works | **removed** |
| `FormatInterface::stringToObject()` | `$data` untyped | `string $data` |
| `getIterator()` | top level of the data, nested values as `stdClass` | 4.0.0 only: `toArray()`; 4.0.1 restored the v3 behaviour |

## Minimum supported PHP version raised

All Framework packages now require **PHP 8.3** or newer.

## The magic property accessors were removed

In v3, `Registry::__get()` and `__set()` existed only to keep the old public `$separator` property
working: `$registry->separator` could be read and written, with a deprecation notice. Both methods
are gone, so in v4 that access throws:

```php
// v3: works, with a deprecation notice. v4: Error: Cannot access protected property
$registry->separator;
$registry->separator = '/';

// v4: pass the separator to the constructor instead
$registry = new Registry(null, '/');
```

The magic methods never gave access to the registry's **data**. In both versions,
`$registry->database` does not read the `database` entry: v3 raised a notice and returned `null`,
v4 raises PHP's *Undefined property* warning and returns `null`. Writing `$registry->database = …`
creates a stray object property (deprecated in v3 and by PHP 8.2+) and leaves the data unchanged.
If you find such code while upgrading, it was already broken; use the path API:

```php
$registry->get('database');
$registry->set('database', 'mysql');
```

To find remaining uses of the separator property:

```bash
grep -rn -- '->separator' src/
```

## The per-call `$separator` argument was removed

`set()` and `loadArray()` accepted a separator that overrode the instance's own for that one call:

```php
// Removed in 4.0.0
$registry->set('a/b/c', $value, '/');
$registry->loadArray($data, true, '/');

// Set the separator on the instance instead
$registry = new Registry(null, '/');
$registry->set('a/b/c', $value);
```

The separator is a constructor argument and applies to the whole instance. If you mixed separators
on one registry, build a second instance for the other convention.

## `FormatInterface::stringToObject()` declares `string $data`

```php
// v3
public function stringToObject($data, array $options = []);

// v4
public function stringToObject(string $data, array $options = []);
```

All five bundled formats — `Ini`, `Json`, `Php`, `Xml`, `Yaml` — were updated to match. **A custom
format class must add the type as well**, otherwise PHP refuses to load it: an implementation may
omit a parameter type the interface declares, but it may not declare a different one, and an
untyped parameter is allowed. In practice adding `string` is the safe change.

Passing `null` now raises a `TypeError` rather than being coerced — worth noting because
`Registry::loadFile()` still hands the result of `file_get_contents()` straight through, and that
is `false` for an unreadable file.

## `getIterator()` in 4.0.0

Release 4.0.0 changed `getIterator()` to iterate `toArray()`, so nested levels came back as arrays
instead of `stdClass` objects. 4.0.1 reverted this, and from 4.0.1 on iteration behaves as in v3:

```php
foreach ($registry as $key => $value) {
    // v3 and 4.0.1+: the top-level entries; nested levels are stdClass objects
    // 4.0.0 only:    nested levels are arrays
}
```

Require at least 4.0.1 rather than 4.0.0 if your code iterates a registry.

## Dependency changes

| Package | v3 (3.0.1) | v4 (4.0.0) |
|---|---|---|
| `php` | `^8.1.0` | `^8.3.0` |
| `joomla/utilities` | `^3.0` | `^4.0` |

`symfony/yaml`, `ext-json` and `ext-simplexml` remain optional, in `suggest`. Release 4.1 adds
`joomla/crypt` and `joomla/console` there for the optional [keychain](keychain.md).

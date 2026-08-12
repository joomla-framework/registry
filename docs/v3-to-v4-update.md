# Updating from v3 to v4

Release 4.0.0 raises the PHP requirement and removes the backwards-compatibility layers that were
deprecated in 2.0 and 3.0: the `$separator` parameters, the magic property accessors, and the
untyped `stringToObject()` signature.

## At a glance

| | v3 (3.0.1) | v4 (4.0.0) |
|---|---|---|
| PHP | `^8.1.0` | `^8.3.0` |
| `$registry->foo` read/write | deprecated, works | **removed** |
| `set()` / `loadArray()` `$separator` argument | deprecated, works | **removed** |
| `FormatInterface::stringToObject()` | `$data` untyped | `string $data` |
| `getIterator()` | iterated the raw object | iterates `toArray()` |

## Minimum supported PHP version raised

All Framework packages now require **PHP 8.3** or newer.

## The magic property accessors were removed

`Registry::__get()` and `__set()` proxied property access onto the internal data and emitted a
deprecation notice. Both are gone:

```php
// Removed in 4.0.0
$registry->database;
$registry->database = 'mysql';

// Use the path API
$registry->get('database');
$registry->set('database', 'mysql');
```

Reading now raises *Undefined property* and evaluates to `null`, so the failure typically surfaces
later as an error on `null`. To find the call sites:

```bash
grep -rnE '\$[a-zA-Z_]+(config|registry|params|state)\->[a-z]' src/
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

## `getIterator()` returns the flattened array

```php
// v3
return new \ArrayIterator($this->data);      // the raw stdClass

// v4
return new \ArrayIterator($this->toArray()); // nested arrays throughout
```

Iterating a registry now yields arrays where it previously yielded `stdClass` objects for nested
levels:

```php
foreach ($registry as $key => $value) {
    // v3: $value could be a stdClass
    // v4: $value is an array
}
```

Note that `count($registry)` still counts only the top level, so it does not match
`iterator_count($registry)`.

## Dependency changes

| Package | v3 (3.0.1) | v4 (4.0.0) |
|---|---|---|
| `php` | `^8.1.0` | `^8.3.0` |
| `joomla/utilities` | `^3.0` | `^4.0` |

`symfony/yaml`, `ext-json`, `ext-simplexml` and `joomla/crypt` remain optional, in `suggest`.

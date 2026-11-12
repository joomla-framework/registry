## Updating from Version 4 to Version 5

The following changes were made to the Registry package between version 4 and version 5.

### The JSON format no longer falls back to INI

`Format\Json::stringToObject()`, and with it `Registry::loadString()`, `Registry::loadFile()` and the constructor with
the default `JSON` format, used to parse a string that was not valid JSON and did not start with `{` as INI. Such a
string now throws a `RuntimeException` ("Error decoding JSON data"). The fallback was deprecated in 4.1.

Load INI data with the INI format:

```php
// 4.x: worked through the fallback, with a deprecation notice from 4.1 on
$registry->loadString("[section]\nfoo=bar");

// 5.0
$registry->loadString("[section]\nfoo=bar", 'INI', ['processSections' => true]);
```

The `iniFallback` option introduced in 4.1 is ignored.

### `get()` returns a stored empty string

`Registry::get()` used to return the default value not only for a missing path or a `null` value, but also for a
stored empty string. It now returns the empty string; the default is only used for a missing path or `null`.

```php
$registry->set('prefix', '');

// 4.x: 'jos_'
// 5.0: ''
$registry->get('prefix', 'jos_');
```

`def()` is built on `get()`, so it now keeps a stored empty string instead of replacing it with the default. Code that
relied on `''` meaning "not set" has to check for it itself, for example with `$registry->get('prefix') ?: 'jos_'`.

### `exists()` is true for a `null` value

`Registry::exists()` followed `isset()` and returned `false` for a path that was set to `null`. It now returns `true`
for every path that has been set, whatever its value. Array access keeps the `isset()` semantics:
`isset($registry['key'])` is still `false` for a `null` value.

```php
$registry->set('key', null);

// 4.x: false, 5.0: true
$registry->exists('key');

// 4.x and 5.0: false
isset($registry['key']);
```

### Native parameter and return types

All methods of `Registry`, `Factory`, `FormatInterface` and the bundled formats now declare native parameter and return
types, and the `#[\ReturnTypeWillChange]` attributes are gone. The main signatures:

```php
// FormatInterface
public function objectToString(object $object, array $options = []): string;
public function stringToObject(string $data, array $options = []): object;

// Registry
public function get(string $path, mixed $default = null): mixed;
public function set(string $path, mixed $value): mixed;
public function exists(string $path): bool;
public function loadString(string $data, string $format = 'JSON', array $options = []): static;
public function loadFile(string $file, string $format = 'JSON', array $options = []): static;
public function toString(string $format = 'JSON', array $options = []): string;

// Factory
public static function getFormat(string $type, array $options = []): FormatInterface;
```

What to check:

* **Custom formats** must add the return types `string` and `object` to `objectToString()` and `stringToObject()`;
  without them PHP refuses to load the class. The `object` type of `$object` may be left out or widened, as
  `Format\Yaml` does with `object|array`.
* **Subclasses of `Registry`** that override a method must declare a compatible return type, for example `: mixed`
  for `get()` and `: static` for the `load*()` methods.
* **Callers with `strict_types`** get a `TypeError` for arguments of the wrong type, for example an integer path in
  `get(0)` or `null` in `loadString(null)`. Without `strict_types`, scalars are converted as before. Array access with
  integer keys (`$registry[0]`) keeps working.

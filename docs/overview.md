## Overview

The Language package provides an interface for managing internationalisation support and translations within an application.
The base objects in the package, `Language` and `Text` are the primary classes used for configuring your application's
language data and translating language keys. Additionally, support for [stemming](http://en.wikipedia.org/wiki/Stemming)
and [transliteration](http://en.wikipedia.org/wiki/Transliteration) is also available via the `Stemmer` and `Transliterate` classes.

### Language Package Structure

A language package contains data to configure and customise the language handling and translations for a given language.
The base package requires the following files (note that `xx-XX` is used to represent a language code):

- `xx-XX.ini` - The base language strings for an application.
- `xx-XX.localise.php` - A localisation class to define language specific behaviours. This class must implement the `LocaliseInterface`.
- `xx-XX.xml` - The metadata for a language.

The base path to language packages must be specified when instantiating a `Language` instance. Language packages must be
stored in a `language` folder within this base path, and each language stored in a separate folder by langauge code. For example:

```php
use Joomla\Language\Language;

$language = new Language('/var/www/jfw-application', 'en-GB');
```

This will create a `Language` instance for the `en-GB` language and specifies that the base folder path is `/var/www/jfw-application`.
Therefore, the XML metadata file should be stored to `/var/www/jfw-application/language/en-GB/en-GB.xml`.

### Translating Language Keys

The `Text` class is responsible for translating language keys into a human friendly text string in the requested language.
Instantiating a `Text` class requires a `Language` instance is injected in the constructor. For convenience, a `Text` instance
can be retrieved via `Language::getText()`. More information about using the `Text` class can be found in the
[class documentation](classes/Text.md).

### Language Key Format

In order to be properly translated, language keys must match a specific format. Generally, a key must match the
`#^[A-Z][A-Z0-9_\-\.]*\s*` regex. Broken down, the first character of the key must be a letter, then any letter or number
are considered valid characters as well as an underscore (`_`), hyphen (`-`), or period (`.`). Only uppercase letters
are accepted. A language file can be debugged using the `Language::debugFile()` method. If `debugFile()` indicates there
are errors, the error list can be retrieved via `Language::getErrorFiles()`.

```php
use Joomla\Language\Language;

$language = new Language('/var/www/jfw-application', 'en-GB');

if (count($language->debugFile('/var/www/jfw-application/language/en-GB/en-GB.ini'))
{
	$errors = $language->getErrorFiles();

	// Application logic to display errors
}
```

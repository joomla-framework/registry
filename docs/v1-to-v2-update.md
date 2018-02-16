## Updating from v1 to v2

The following changes were made to the Language package between v1 and v2.

### Class methods non-static

The `Language` and `Text` classes have been refactored to follow an object oriented approach instead of using static methods.
Instantiating the `Text` class requires a `Language` instance to be injected.

### Singleton object storage removed

The package no longer supports singleton object storage. Calling the `LanguageFactory` will always return a new instance of
the requested object.

### Language requires base path to be defined

In v1, applications were required to define a `JPATH_ROOT` constant for the base path to search for languages in. In 2.x,
you are required to pass the base path as the first parameter in the `Language` constructor (and getInstance() method).

### _QQ_ constant removed

The `_QQ_` constant was previously an allowed escape sequence for quotes in language files.  Support for this constant
has been removed. Double quotes in files should either be escaped (`\"`) or HTML encoded (`&quot;`) (if displayed in an
HTML context).

### Language::_ deprecated

The `_` method in `Language` has been deprecated in favor of `translate`.

### Language::getInstance() removed

The `getInstance()` method in `Language` has been removed.  Use `LanguageFactory::getLanguage()` instead.

### Language::setLanguage() removed

The `setLanguage()` method in `Language` has been removed.  A new Language instance in the new language should be instantiated
instead.

### Language::$strings removed

The `$strings` class member variable in `Language` has been removed.  Loaded translation strings are now stored in a `MessageCatalogue`
instance.

### Abstract Stemmer removed

The abstract `Stemmer` class has been removed. Stemmers are now defined by a new `StemmerInterface`. Use `LanguageFactory::getStemmer()`
to get an instance of a `Stemmer` as a replacement for `Stemmer::getInstance()`.

### Text::alt() signature changed

The `alt()` method's signature has had a backward compatibility breaking change.  A `$parameters` parameter has been added and is
now the third parameter in the method signature.  This affects calls to the method which included the `$jsSafe` and
`$interpretBackSlashes` parameters.

### Text::script() removed

The `script()` method in `Text` has been removed, as well as support for the internal JavaScript store.  Downstream applications
should implement this feature if needed.

### Text::_() removed

The `_()` method in `Text` has been removed. Because the package no longer supports singleton objects, it is not possible to proxy
this to the non-static API.

### Methods for CMS Search Component

The methods `getUpperLimitSearchWord`, `getSearchDisplayedCharactersNumber`, `getLowerLimitSearchWord` and `getIgnoredSearchWords`
have all been removed as well as the associated methods to get and set their callback functions. These should be implemented
by the user if required.

### LocaliseInterface
A `LocaliseInterface` has been introduced as a standard way to set the way to set functions for `Language::getPluralSuffixes()`
and `Language::transliterate()`. Both of these must now be set in the language's localise.php file (this was possible in
version 1 however could optionally could have callback functions set if such a file didn't exist).

Localise files now MUST implement the LocaliseInterface to be recognised. An base class is available and is suitable for
many Western Languages.

### Message Key Normalisation
In 1.x, when retrieving translation strings from the internal store, the key was normalled to full uppercase when loaded, blocking
use of mixed case strings. In 2.0, keys are normalised to uppercase both when messages are written to a catalogue and when read out
of the catalogue. If desired, a catalogue allowing mixed case strings can be created by subclassing the `MessageCatalogue` class.

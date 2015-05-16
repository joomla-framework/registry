## Text

The `Text` class is the Language package's translation interface and is used for translating language keys to a
requested language. Instantiating the `Text` class requires a `Language` instance to be injected.

### Instantiating Text

There are several ways to instantiate a `Text` object.  The class can be instantiated directly, created via `Language::getText()`,
or a shared instance may be retrieved from a `Joomla\DI\Container`.

#### Example 1: Creating a Text Object

```php
use Joomla\Language\Language;
use Joomla\Language\Text;

$language = new Language('/var/www/jfw-application', 'en-GB');
$text     = new Text($language);
```

#### Example 2: Loading a Text Object from a Language instance

```php
use Joomla\Language\Language;

$language = new Language('/var/www/jfw-application', 'en-GB');
$text     = $language->getText();
```

#### Example 3: Loading a Text Object from the DI Container

Note: In order to use this method, your project must utilise Joomla's [Dependency Injection](https://github.com/joomla-framework/di)
package.  A `LanguageFactory` object must also be stored in the container.

```php
use Joomla\DI\Container;
use Joomla\Language\Service\LanguageFactoryProvider;

$container = new Container;
$container->registerServiceProvider(new LanguageFactoryProvider);

$languageFactory = $container->get('Joomla\Language\LanguageFactory');
$languageFactory->setLanguageDirectory('/var/www/jfw-application');

// This will also create a Language instance in the LanguageFactory in the default language
$text = $languageFactory->getText();
```

### Translating a Key

The `translate` method is used for basic translations of language keys. The method requires the key to be supplied for
translation and also has several optional parameters.

```php
/*
 * @param   string   $string                The string to translate.
 * @param   array    $parameters            Array of parameters for the string
 * @param   array    $jsSafe                Array containing data to make the string safe for JavaScript output
 * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
 */
public function translate($string, $parameters = array(), $jsSafe = array(), $interpretBackSlashes = true)
```

The following example demonstrates basic usage of the `Text` class.

```php
use Joomla\Language\Language;
use Joomla\Language\Text;

$language = new Language('/var/www/jfw-application', 'en-GB');
$text     = new Text($language);

$translatedString = $text->translate('MY_KEY');
```

If the supplied key is found in the `Language` class storage, the translated string will be returned; otherwise the
key will be returned.

#### Named Parameter Support

A new feature in 2.0 is support for named parameters.  The second parameter in the `translate` method accepts an
associative array where the key is the string to replace and the value is the replacement.

Assuming the following is the contents of the `en-GB.ini` language file:

```ini
MY_KEY="%term% Rocks!"
```

The following example demonstrates usage of the `translate` method with named parameters.

```php
use Joomla\Language\Language;
use Joomla\Language\Text;

$language = new Language('/var/www/jfw-application', 'en-GB');
$text     = new Text($language);

// Will return "Joomla Rocks!"
$translatedAltString = $text->translate('MY_KEY', array('%term%' => 'Joomla');
```


### Alternate Translations

The `alt` method is used for creating potential alternate translations of a base language key by specifying a possible
suffix for the language key.  If a language key with the specified suffix is found, the translated string for this key
is returned, otherwise the base language key will be processed for translation.

```php
/*
 * @param   string   $string                The string to translate.
 * @param   string   $alt                   The alternate option for global string
 * @param   array    $parameters            Array of parameters for the string
 * @param   array    $jsSafe                Array containing data to make the string safe for JavaScript output
 * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
 */
public function alt($string, $parameters = array(), $alt, $jsSafe = false, $interpretBackSlashes = true)
```

Assuming the following is the contents of the `en-GB.ini` language file:

```ini
MY_KEY="Foo"
MY_KEY_ROCKS="Bar"
```

The following example demonstrates usage of the `alt` method.

```php
use Joomla\Language\Language;
use Joomla\Language\Text;

$language = new Language('/var/www/jfw-application', 'en-GB');
$text     = new Text($language);

// Will return "Bar"
$translatedAltString = $text->alt('MY_KEY', 'ROCKS');

// Will return "Foo"
$translatedBaseString = $text->alt('MY_KEY', 'IS_COOL');
```

The `alt` method also supports named parameters.

## LanguageFactory

The `LanguageFactory` class is the Language package's service provider and is used to retrieve objects provided by the Language
package.

### Instantiating LanguageFactory

There are two ways to instantiate a `LanguageFactory` object.  The class can be instantiated directly or a shared instance may
be retrieved from a `Joomla\DI\Container`.

#### Example 1: Creating a LanguageFactory Object

```php
use Joomla\Language\LanguageFactory;

$languageFactory = new LanguageFactory;
```

#### Example 2: Loading a Text Object from the DI Container

Note: In order to use this method, your project must utilise Joomla's [Dependency Injection](https://github.com/joomla-framework/di)
package.

```php
use Joomla\DI\Container;
use Joomla\Language\Service\LanguageFactoryProvider;

$container = new Container;
$container->registerServiceProvider(new LanguageFactoryProvider);

$languageFactory = $container->get('Joomla\Language\LanguageFactory');
```

The `LanguageFactory` object may be further configured if a `config` object is registered to the DI Container.  The `config`
object must be an instance of `Joomla\Registry\Registry`.

```php
use Joomla\DI\Container;
use Joomla\Language\Service\LanguageFactoryProvider;
use Joomla\Registry\Registry;

$config = new Registry;
$config->set('language.basedir', '/var/www/jfw-application');
$config->set('language.default', 'en-GB');

$container = new Container;
$container->set('config', $config);
$container->registerServiceProvider(new LanguageFactoryProvider);

$languageFactory = $container->get('Joomla\Language\LanguageFactory');
```

### Retrieving a Language instance

The `getLanguage` method is used to retrieve a `Language` instance.  The factory caches a `Language` instance for each language
that has been requested.  

```php
/*
 * @param   string   $lang   The language to use.
 * @param   string   $path   The base path to the language folder.  This is required if creating a new instance.
 * @param   boolean  $debug  The debug mode.
 */
public function getLanguage($lang = null, $path = null, $debug = false)
```

If the `$lang` or `$path` parameters are not set, the default values stored in the factory instance will be used instead.

The following example demonstrates basic usage of the `LanguageFactory` class to retrieve a Language instance.  This assumes
a Factory instance has been stored to a DI container as demonstrated above.

```php
$languageFactory = $container->get('Joomla\Language\LanguageFactory');

$language = $languageFactory->getLangauge();
```

### Retrieving a LocaliseInterface instance

The `getLocalise` method is used to retrieve a `LocaliseInterface` instance.

```php
/*
 * @param   string  $lang      Language to check.
 * @param   string  $basePath  Base path to the language folder.
 */
public function getLocalise($lang, $basePath = null)
```

If the `$basePath` parameter is not set, the default language path stored in the factory instance will be used instead.

The following example demonstrates basic usage of the `LanguageFactory` class to retrieve a LocaliseInterface instance.
This assumes a Factory instance has been stored to a DI container as demonstrated above.

```php
$languageFactory = $container->get('Joomla\Language\LanguageFactory');

$language = $languageFactory->getLocalise('en-GB');
```

Note that if a class is found at the lookup path and does not implement the `LocaliseInterface` a `RuntimeException` is thrown.
If a `LocaliseInterface` object is not found in the lookup path, a `En_GBLocalise` object will be returned as a default implementation.

# The Language Package [![Build Status](https://travis-ci.org/joomla-framework/language.png?branch=master)](https://travis-ci.org/joomla-framework/language) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/joomla-framework/language/badges/quality-score.png?b=2.0-dev)](https://scrutinizer-ci.com/g/joomla-framework/language/?branch=2.0-dev)

## Usage


### Prepare language files

Let's say you want to use English (UK) language pack.

You may find helpful some [files distributed](https://github.com/joomla/joomla-cms/tree/master/language/en-GB) with Joomla CMS:

- `en-GB.ini` - Common language strings such as `JYES`, `ERROR`, `JLOGIN`.
- `en-GB.lib_joomla.ini` - Application language strings like `JLIB_APPLICATION_SAVE_SUCCESS` ('Item successfully saved.').
- `en-GB.localise.php` - To use language-specific methods like `getIgnoredSearchWords`.
- `en-GB.xml` - To use Language metadata definitions (full name, rtl, locale, firstDay).

In the Framework version `1.*` Language handler loads language files from directory defined by `JPATH_ROOT . '/languages/[language tag]/`

In the example below, we will additinally load your application language strings located in file `en-GB.application.ini`.


### Prepare configuration

Assuming configuration in a `JSON` format:

```JSON
{
	"lang": "en-GB",
	"debug": true
}
```

### Set up the Language instance in Web application

_In the example below comments in language tag are replaced by `xx-XX`_

```PHP
use Joomla\Application\AbstractWebApplication;
use Joomla\Language\Language;
use Joomla\Language\Text;

class MyApplication extends AbstractWebApplication
{
	protected $language;

	protected function initialise()
	{
		parent::initialise();

		$language = $this->getLanguage();

		// Load xx-XX/xx-XX.application.ini file
		$language->load('application');
	}

	/**
	 * Get language object.
	 *
	 * @return  Language
	 *
	 * @note    The base path to the directory where your language files are stored must be injected into the Language object
	 */
	protected function getLanguage()
	{
		if (is_null($this->language))
		{
			// Get language object with the lang tag and debug setting in your configuration
			// This also loads language file /xx-XX/xx-XX.ini and localisation methods /xx-XX/xx-XX.localise.php if available
			$language = Language::getInstance($this->get('language'), $basePath, $this->get('debug'));

			$this->language = $language;
		}

		return $this->language;
	}
}

```

### Use `Text` methods

A `Text` instance can be retrieved via `Language::getText()` which injects the active Language instance into it for quick use 

```php
namespace App\Hello\Controller;

use Joomla\Language\Text
use Joomla\Controller\AbstractController;

class HelloController extends AbstractController
{
	public function execute()
	{
		$app = $this->getApplication();

		$translatedString = Language::getInstance()->getText()->translate('APP_HELLO_WORLD');

		$app->setBody($translatedString);
	}
}

```

`Text` objects require a `Language` instance to be instantiated

```php
namespace App\Hello\Controller;

use Joomla\Language\Text
use Joomla\Controller\AbstractController;

class HelloController extends AbstractController
{
	public function execute()
	{
		$app = $this->getApplication();
		$text = new Text($this->getLanguage());

		$translatedString = $text->translate('APP_HELLO_WORLD');

		$app->setBody($translatedString);
	}
}

```

### Using `Text` From Twig ###

If you are using [Twig](http://twig.sensiolabs.org/) as a templating engine you will be unable to execute PHP code in the layout to use the `Text` magic function directly.  One option is to add each string to the view, however you can also use Twig functions.

Creating a Twig function will allow the use of syntax like the below in your layout file.

	jtext('APP_YOURSAMPLE_STRING')

To create a Twig function to do this after creating the Twig_Environment and before rendering add the following code:

	$loader = new \Twig_Loader_Filesystem($this->path);
	$twig = new \Twig_Environment($loader);
	$text = Language::getInstance()->getText();

	$jtextFunction = new \Twig_SimpleFunction('jtext', function($string, $text) {
		return $text->translate($string);
	}, array('is_safe'=>array('html')));

	$twig->addFunction($jtextFunction);

You will now be able translate strings in your twig file using:

	{{jtext('APP_YOURSAMPLE_STRING')}}


### Load component language files

@TODO

## Changes From 1.x

The following changes have been made to the `Language` package since 1.x.

### Class methods non-static

The `Language` and `Text` classes have been refactored to follow an object oriented approach instead of static methods.  Instantiating the `Text` class requires a `Language` instance to be injected.

The `Text::_()` method remains static and will proxy to the object oriented API as long as a Language instance is available via `Language::getInstance()`.

### Language requires base path to be defined

In 1.x, applications were required to define a `JPATH_ROOT` constant for the base path to search for languages in.  In 2.x, you are required to pass the base path as the first parameter in the `Language` constructor (and getInstance() method).

### `_QQ_` constant removed

The `_QQ_` constant was previous an allowed escape sequence for quotes in language files.  Support for this constant has been removed.  Double quotes in files should either be escaped (`\"`) or HTML encoded (`&quot;`) (if displayed in an HTML context).

### Language::_ and Text::_ deprecated

The `_` method in `Language` and `Text` has been deprecated in favor of `translate`.

### `Language::setLanguage()` removed

The `setLanguage()` method in `Language` has been removed.  A new Language instance in the new language should be instantiated instead.

### Methods for Search Component

The methods `getUpperLimitSearchWord`, `getSearchDisplayedCharactersNumber`, `getLowerLimitSearchWord` and `getIgnoredSearchWords` have all been removed as well as the associated methods to get and set their callback functions. These should be implemented by the user if required.

### Localise Interface
This has been introduced as a standard way to set the way to set functions for ```Language::getPluralSuffixes()``` and ```Language::transliterate()```. Both these must now be set in the language's localise.php file (this was possible in Language version 1 however could optionally could have callback functions set if such a file didn't exist).

Localise files now MUST implement this interface to be recognized. An abstract version is available suitable for most Western Languages.

## Installation via Composer

Add `"joomla/language": "2.0.*@dev"` to the require block in your composer.json and then run `composer install`.

```json
{
	"require": {
		"joomla/language": "2.0.*@dev"
	}
}
```

Alternatively, you can simply run the following from the command line:

```sh
composer require joomla/language "2.0.*@dev"
```

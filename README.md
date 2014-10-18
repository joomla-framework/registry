# The Language Package [![Build Status](https://travis-ci.org/joomla-framework/language.png?branch=master)](https://travis-ci.org/joomla-framework/language)

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
	 * @note    JPATH_ROOT has to be defined.
	 */
	protected function getLanguage()
	{
		if (is_null($this->language))
		{
			// Get language object with the lang tag and debug setting in your configuration
			// This also loads language file /xx-XX/xx-XX.ini and localisation methods /xx-XX/xx-XX.localise.php if available
			$language = Language::getInstance($this->get('language'), $this->get('debug'));

			// Configure Text to use language instance
			Text::setLanguage($language);

			$this->language = $language;
		}

		return $this->language;
	}
}

```

### Use `Text` methods

```PHP
namespace App\Hello\Controller;

use Joomla\Language\Text
use Joomla\Controller\AbstractController;

class HelloController extends AbstractController
{
	public function execute()
	{
		$app = $this->getApplication();

		$translatedString = Text::_('APP_HELLO_WORLD');

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

	$jtextFunction = new \Twig_SimpleFunction('jtext',function($string){
		$translation = Text::_($string);
		return $translation;
	},array('is_safe'=>array('html')));

	$twig->addFunction($jtextFunction);

You will now be able translate strings in your twig file using:

	{{jtext('APP_YOURSAMPLE_STRING')}}



### Load component language files

@TODO


## Changes From 1.x

The following changes have been made to the `Language` package since 1.x.

### Text class methods non-static

The `Text` class has been refactored to follow an object oriented approach instead of static methods.  Instantiating the class requires a `Language` instance to be injected.

### Text::_ deprecated

The `_` method in `Text` has been deprecated in favor of `translate`.

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

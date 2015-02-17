<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Text handling class.
 *
 * @since  1.0
 */
class Text
{
	/**
	 * JavaScript strings
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $strings = array();

	/**
	 * Language instance
	 *
	 * @var    Language
	 * @since  1.0
	 */
	private $language;

	/**
	 * Constructor
	 *
	 * @param   Language  $language  Language instance to use in translations
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(Language $language)
	{
		$this->setLanguage($language);
	}

	/**
	 * Retrieve the current Language instance
	 *
	 * @return  Language
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Set the Language object
	 *
	 * @param   Language  $language  Language instance
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setLanguage(Language $language)
	{
		$this->language = $language;

		return $this;
	}

	/**
	 * Translates a string into the current language.
	 *
	 * @param   string   $string                The string to translate.
	 * @param   mixed    $jsSafe                Boolean: Make the result javascript safe.
	 * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
	 * @param   boolean  $script                To indicate that the string will be push in the javascript language store
	 *
	 * @return  string  The translated string or the key if $script is true
	 *
	 * @see     Text::translate()
	 * @since   1.0
	 * @deprecated  3.0  Use translate instead
	 */
	public static function _($string, $jsSafe = false, $interpretBackSlashes = true, $script = false)
	{
		$factory = new LanguageFactory;
		$text    = $factory->getText();

		return $text->translate($string, $jsSafe, $interpretBackSlashes, $script);
	}

	/**
	 * Translates a string into the current language.
	 *
	 * @param   string   $string                The string to translate.
	 * @param   array    $jsSafe                Array containing data to make the string safe for JavaScript output
	 * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
	 * @param   boolean  $script                To indicate that the string will be push in the javascript language store
	 *
	 * @return  string  The translated string or the key if $script is true
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function translate($string, $jsSafe = array(), $interpretBackSlashes = true, $script = false)
	{
		$lang = $this->getLanguage();

		if (!empty($jsSafe))
		{
			if (array_key_exists('interpretBackSlashes', $jsSafe))
			{
				$interpretBackSlashes = (boolean) $jsSafe['interpretBackSlashes'];
			}

			if (array_key_exists('script', $jsSafe))
			{
				$script = (boolean) $jsSafe['script'];
			}

			if (array_key_exists('jsSafe', $jsSafe))
			{
				$jsSafe = (boolean) $jsSafe['jsSafe'];
			}
			else
			{
				$jsSafe = false;
			}
		}

		if ($script)
		{
			$this->strings[$string] = $lang->translate($string, $jsSafe, $interpretBackSlashes);

			return $string;
		}

		return $lang->translate($string, $jsSafe, $interpretBackSlashes);
	}

	/**
	 * Translates a string into the current language.
	 *
	 * @param   string   $string                The string to translate.
	 * @param   string   $alt                   The alternate option for global string
	 * @param   mixed    $jsSafe                Boolean: Make the result javascript safe.
	 * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
	 * @param   boolean  $script                To indicate that the string will be pushed in the javascript language store
	 *
	 * @return  string  The translated string or the key if $script is true
	 *
	 * @since   1.0
	 */
	public function alt($string, $alt, $jsSafe = false, $interpretBackSlashes = true, $script = false)
	{
		$lang = $this->getLanguage();

		if ($lang->hasKey($string . '_' . $alt))
		{
			return $this->translate($string . '_' . $alt, $jsSafe, $interpretBackSlashes, $script);
		}

		return $this->translate($string, $jsSafe, $interpretBackSlashes, $script);
	}

	/**
	 * Pluralises a string in the current language
	 *
	 * The last argument can take an array of options:
	 *
	 * array('jsSafe'=>boolean, 'interpretBackSlashes'=>boolean, 'script'=>boolean)
	 *
	 * where:
	 *
	 * jsSafe is a boolean to generate a javascript safe strings.
	 * interpretBackSlashes is a boolean to interpret backslashes \\->\, \n->new line, \t->tabulation.
	 * script is a boolean to indicate that the string will be push in the javascript language store.
	 *
	 * @param   string   $string  The format string.
	 * @param   integer  $n       The number of items
	 *
	 * @return  string  The translated strings or the key if 'script' is true in the array of options
	 *
	 * @note    This method can take a mixed number of arguments for the sprintf function
	 * @since   1.0
	 */
	public function plural($string, $n)
	{
		$lang = $this->getLanguage();
		$args = func_get_args();
		$count = count($args);

		// Try the key from the language plural potential suffixes
		$found = false;
		$suffixes = $lang->getPluralSuffixes((int) $n);
		array_unshift($suffixes, (int) $n);

		foreach ($suffixes as $suffix)
		{
			$key = $string . '_' . $suffix;

			if ($lang->hasKey($key))
			{
				$found = true;
				break;
			}
		}

		if (!$found)
		{
			// Not found so revert to the original.
			$key = $string;
		}

		if (is_array($args[$count - 1]))
		{
			$args[0] = $lang->translate(
				$key, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe'] : false,
				array_key_exists('interpretBackSlashes', $args[$count - 1]) ? $args[$count - 1]['interpretBackSlashes'] : true
			);

			if (array_key_exists('script', $args[$count - 1]) && $args[$count - 1]['script'])
			{
				$this->strings[$key] = call_user_func_array('sprintf', $args);

				return $key;
			}
		}
		else
		{
			$args[0] = $lang->translate($key);
		}

		return call_user_func_array('sprintf', $args);
	}

	/**
	 * Passes a string thru a sprintf.
	 *
	 * The last argument can take an array of options:
	 *
	 * array('jsSafe'=>boolean, 'interpretBackSlashes'=>boolean, 'script'=>boolean)
	 *
	 * where:
	 *
	 * jsSafe is a boolean to generate a javascript safe strings.
	 * interpretBackSlashes is a boolean to interpret backslashes \\->\, \n->new line, \t->tabulation.
	 * script is a boolean to indicate that the string will be push in the javascript language store.
	 *
	 * @param   string  $string  The format string.
	 *
	 * @return  string|null  The translated strings or the key if 'script' is true in the array of options.
	 *
	 * @note    This method can take a mixed number of arguments for the sprintf function
	 * @since   1.0
	 */
	public function sprintf($string)
	{
		$lang = $this->getLanguage();
		$args = func_get_args();
		$count = count($args);

		if (is_array($args[$count - 1]))
		{
			$args[0] = $lang->translate(
				$string, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe'] : false,
				array_key_exists('interpretBackSlashes', $args[$count - 1]) ? $args[$count - 1]['interpretBackSlashes'] : true
			);

			if (array_key_exists('script', $args[$count - 1]) && $args[$count - 1]['script'])
			{
				$this->strings[$string] = call_user_func_array('sprintf', $args);

				return $string;
			}
		}
		else
		{
			$args[0] = $lang->translate($string);
		}

		return call_user_func_array('sprintf', $args);
	}

	/**
	 * Passes a string thru an printf.
	 *
	 * The last argument can take an array of options:
	 *
	 * array('jsSafe'=>boolean, 'interpretBackSlashes'=>boolean, 'script'=>boolean)
	 *
	 * where:
	 *
	 * jsSafe is a boolean to generate a javascript safe strings.
	 * interpretBackSlashes is a boolean to interpret backslashes \\->\, \n->new line, \t->tabulation.
	 * script is a boolean to indicate that the string will be push in the javascript language store.
	 *
	 * @param   string  $string  The format string.
	 *
	 * @return  string|null  The translated strings or the key if 'script' is true in the array of options.
	 *
	 * @note    This method can take a mixed number of arguments for the printf function
	 * @since   1.0
	 */
	public function printf($string)
	{
		$lang = $this->getLanguage();
		$args = func_get_args();
		$count = count($args);

		if (is_array($args[$count - 1]))
		{
			$args[0] = $lang->translate(
				$string, array_key_exists('jsSafe', $args[$count - 1]) ? $args[$count - 1]['jsSafe'] : false,
				array_key_exists('interpretBackSlashes', $args[$count - 1]) ? $args[$count - 1]['interpretBackSlashes'] : true
			);
		}
		else
		{
			$args[0] = $lang->translate($string);
		}

		return call_user_func_array('printf', $args);
	}

	/**
	 * Translate a string into the current language and stores it in the JavaScript language store.
	 *
	 * @param   string   $string                The Text key.
	 * @param   array    $jsSafe                Ensure the output is JavaScript safe.
	 * @param   boolean  $interpretBackSlashes  Interpret \t and \n.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function script($string = null, $jsSafe = array(), $interpretBackSlashes = true)
	{
		// Add the string to the array if not null.
		if ($string !== null)
		{
			if (is_array($jsSafe))
			{
				if (array_key_exists('interpretBackSlashes', $jsSafe))
				{
					$interpretBackSlashes = (boolean) $jsSafe['interpretBackSlashes'];
				}

				if (array_key_exists('jsSafe', $jsSafe))
				{
					$jsSafe = (boolean) $jsSafe['jsSafe'];
				}
				else
				{
					$jsSafe = false;
				}
			}

			// Normalize the key and translate the string.
			$this->strings[strtoupper($string)] = $this->getLanguage()->translate($string, $jsSafe, $interpretBackSlashes);
		}

		return $this->strings;
	}
}

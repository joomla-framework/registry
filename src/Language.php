<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

use Joomla\String\String;

/**
 * Languages/translation handler class
 *
 * @since  1.0
 */
class Language
{
	/**
	 * Language instance container
	 *
	 * @var    Language[]
	 * @since  1.0
	 */
	protected static $languages = array();

	/**
	 * Debug language, If true, highlights if string isn't found.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $debug = false;

	/**
	 * The default language, used when a language file in the requested language does not exist.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $default = 'en-GB';

	/**
	 * An array of orphaned text.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $orphans = array();

	/**
	 * Array holding the language metadata.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $metadata = null;

	/**
	 * Array holding the language locale or boolean null if none.
	 *
	 * @var    array|boolean
	 * @since  1.0
	 */
	protected $locale = null;

	/**
	 * The language to load.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $lang = null;

	/**
	 * A nested array of language files that have been loaded
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $paths = array();

	/**
	 * List of language files that are in error state
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $errorfiles = array();

	/**
	 * Translations
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $strings = null;

	/**
	 * An array of used text, used during debugging.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $used = array();

	/**
	 * Counter for number of loads.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	protected $counter = 0;

	/**
	 * An array used to store overrides.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $override = array();

	/**
	 * Name of the transliterator function for this language.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $transliterator = null;

	/**
	 * Name of the pluralSuffixesCallback function for this language.
	 *
	 * @var    callable
	 * @since  1.0
	 */
	protected $pluralSuffixesCallback = null;

	/**
	 * Name of the ignoredSearchWordsCallback function for this language.
	 *
	 * @var    callable
	 * @since  1.0
	 */
	protected $ignoredSearchWordsCallback = null;

	/**
	 * Name of the lowerLimitSearchWordCallback function for this language.
	 *
	 * @var    callable
	 * @since  1.0
	 */
	protected $lowerLimitSearchWordCallback = null;

	/**
	 * Name of the uppperLimitSearchWordCallback function for this language
	 *
	 * @var    callable
	 * @since  1.0
	 */
	protected $upperLimitSearchWordCallback = null;

	/**
	 * Name of the searchDisplayedCharactersNumberCallback function for this language.
	 *
	 * @var    callable
	 * @since  1.0
	 */
	protected $searchDisplayedCharactersNumberCallback = null;

	/**
	 * LanguageHelper object
	 *
	 * @var    LanguageHelper
	 * @since  __DEPLOY_VERSION__
	 */
	protected $helper;

	/**
	 * The base path to the language folder
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $basePath;

	/**
	 * Constructor activating the default information of the language.
	 *
	 * @param   string   $path   The base path to the language folder
	 * @param   string   $lang   The language
	 * @param   boolean  $debug  Indicates if language debugging is enabled.
	 *
	 * @since   1.0
	 */
	public function __construct($path, $lang = null, $debug = false)
	{
		$this->basePath = $path;
		$this->strings  = array();
		$this->helper   = new LanguageHelper;

		$lang = ($lang == null) ? $this->default : $lang;

		$this->setLanguage($lang);
		$this->setDebug($debug);

		$basePath = $this->helper->getLanguagePath($this->basePath);

		$filename = $basePath . "/overrides/$lang.override.ini";

		if (file_exists($filename) && $contents = $this->parse($filename))
		{
			if (is_array($contents))
			{
				// Sort the underlying heap by key values to optimize merging
				ksort($contents, SORT_STRING);
				$this->override = $contents;
			}

			unset($contents);
		}

		// Look for a language specific localise class
		$class = str_replace('-', '_', $lang . 'Localise');
		$paths = array();

		$paths[0] = $basePath . "/overrides/$lang.localise.php";
		$paths[1] = $basePath . "/$lang/$lang.localise.php";

		ksort($paths);
		$path = reset($paths);

		while (!class_exists($class) && $path)
		{
			if (file_exists($path))
			{
				require_once $path;
			}

			$path = next($paths);
		}

		if (class_exists($class))
		{
			/* Class exists. Try to find
			 * -a transliterate method,
			 * -a getPluralSuffixes method,
			 * -a getIgnoredSearchWords method
			 * -a getLowerLimitSearchWord method
			 * -a getUpperLimitSearchWord method
			 * -a getSearchDisplayCharactersNumber method
			 */
			if (method_exists($class, 'transliterate'))
			{
				$this->transliterator = array($class, 'transliterate');
			}

			if (method_exists($class, 'getPluralSuffixes'))
			{
				$this->pluralSuffixesCallback = array($class, 'getPluralSuffixes');
			}

			if (method_exists($class, 'getIgnoredSearchWords'))
			{
				$this->ignoredSearchWordsCallback = array($class, 'getIgnoredSearchWords');
			}

			if (method_exists($class, 'getLowerLimitSearchWord'))
			{
				$this->lowerLimitSearchWordCallback = array($class, 'getLowerLimitSearchWord');
			}

			if (method_exists($class, 'getUpperLimitSearchWord'))
			{
				$this->upperLimitSearchWordCallback = array($class, 'getUpperLimitSearchWord');
			}

			if (method_exists($class, 'getSearchDisplayedCharactersNumber'))
			{
				$this->searchDisplayedCharactersNumberCallback = array($class, 'getSearchDisplayedCharactersNumber');
			}
		}

		$this->load();
	}

	/**
	 * Returns a language object.
	 *
	 * @param   string   $lang   The language to use.
	 * @param   string   $path   The base path to the language folder.  This is required if creating a new instance.
	 * @param   boolean  $debug  The debug mode.
	 *
	 * @return  Language  The Language object.
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public static function getInstance($lang = null, $path = null, $debug = false)
	{
		if (!isset(self::$languages[$lang . $debug]))
		{
			if ($path === null)
			{
				throw new \InvalidArgumentException(
					'The $path variable cannot be null when creating a new Language object'
				);
			}

			$language = new self($path, $lang, $debug);

			self::$languages[$lang . $debug] = $language;

			/*
			 * Check if Language was instantiated with a null $lang param;
			 * if so, retrieve the language code from the object and store
			 * the instance with the language code as well
			 */
			if (is_null($lang))
			{
				self::$languages[$language->getLanguage() . $debug] = $language;

				// Set the "default" language object if it isn't
				if (!isset(self::$languages[$debug]))
				{
					self::$languages[$debug] = $language;
				}
			}
		}

		return self::$languages[$lang . $debug];
	}

	/**
	 * Translate function, mimics the php gettext (alias _) function.
	 *
	 * The function checks if $jsSafe is true, then if $interpretBackslashes is true.
	 *
	 * @param   string   $string                The string to translate
	 * @param   boolean  $jsSafe                Make the result javascript safe
	 * @param   boolean  $interpretBackSlashes  Interpret \t and \n
	 *
	 * @return  string  The translation of the string
	 *
	 * @see     Language::translate()
	 * @since   1.0
	 * @deprecated  3.0  Use translate instead
	 */
	public function _($string, $jsSafe = false, $interpretBackSlashes = true)
	{
		return $this->translate($string, $jsSafe, $interpretBackSlashes);
	}

	/**
	 * Translate function, mimics the php gettext (alias _) function.
	 *
	 * The function checks if $jsSafe is true, then if $interpretBackslashes is true.
	 *
	 * @param   string   $string                The string to translate
	 * @param   boolean  $jsSafe                Make the result javascript safe
	 * @param   boolean  $interpretBackSlashes  Interpret \t and \n
	 *
	 * @return  string  The translation of the string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function translate($string, $jsSafe = false, $interpretBackSlashes = true)
	{
		// Detect empty string
		if ($string == '')
		{
			return '';
		}

		$key = strtoupper($string);

		if (isset($this->strings[$key]))
		{
			$string = $this->debug ? '**' . $this->strings[$key] . '**' : $this->strings[$key];

			// Store debug information
			if ($this->debug)
			{
				$caller = $this->getCallerInfo();

				if (!array_key_exists($key, $this->used))
				{
					$this->used[$key] = array();
				}

				$this->used[$key][] = $caller;
			}
		}
		else
		{
			if ($this->debug)
			{
				$caller = $this->getCallerInfo();
				$caller['string'] = $string;

				if (!array_key_exists($key, $this->orphans))
				{
					$this->orphans[$key] = array();
				}

				$this->orphans[$key][] = $caller;

				$string = '??' . $string . '??';
			}
		}

		if ($jsSafe)
		{
			// Javascript filter
			$string = addslashes($string);
		}
		elseif ($interpretBackSlashes)
		{
			// Interpret \n and \t characters
			$string = str_replace(array('\\\\', '\t', '\n'), array("\\", "\t", "\n"), $string);
		}

		return $string;
	}

	/**
	 * Transliterate function
	 *
	 * This method processes a string and replaces all accented UTF-8 characters by unaccented
	 * ASCII-7 "equivalents".
	 *
	 * @param   string  $string  The string to transliterate.
	 *
	 * @return  string  The transliteration of the string.
	 *
	 * @since   1.0
	 */
	public function transliterate($string)
	{
		if ($this->transliterator !== null)
		{
			return call_user_func($this->transliterator, $string);
		}

		$transliterate = new Transliterate;
		$string = $transliterate->utf8_latin_to_ascii($string);
		$string = String::strtolower($string);

		return $string;
	}

	/**
	 * Getter for transliteration function
	 *
	 * @return  callable  The transliterator function
	 *
	 * @since   1.0
	 */
	public function getTransliterator()
	{
		return $this->transliterator;
	}

	/**
	 * Set the transliteration function.
	 *
	 * @param   callable  $function  Function name or the actual function.
	 *
	 * @return  callable  The previous function.
	 *
	 * @since   1.0
	 */
	public function setTransliterator($function)
	{
		$previous = $this->transliterator;
		$this->transliterator = $function;

		return $previous;
	}

	/**
	 * Returns an array of suffixes for plural rules.
	 *
	 * @param   integer  $count  The count number the rule is for.
	 *
	 * @return  array    The array of suffixes.
	 *
	 * @since   1.0
	 */
	public function getPluralSuffixes($count)
	{
		if ($this->pluralSuffixesCallback !== null)
		{
			return call_user_func($this->pluralSuffixesCallback, $count);
		}

		return array((string) $count);
	}

	/**
	 * Getter for pluralSuffixesCallback function.
	 *
	 * @return  callable  Function name or the actual function.
	 *
	 * @since   1.0
	 */
	public function getPluralSuffixesCallback()
	{
		return $this->pluralSuffixesCallback;
	}

	/**
	 * Set the pluralSuffixes function.
	 *
	 * @param   callable  $function  Function name or actual function.
	 *
	 * @return  callable  The previous function.
	 *
	 * @since   1.0
	 */
	public function setPluralSuffixesCallback($function)
	{
		$previous = $this->pluralSuffixesCallback;
		$this->pluralSuffixesCallback = $function;

		return $previous;
	}

	/**
	 * Returns an array of ignored search words
	 *
	 * @return  array  The array of ignored search words.
	 *
	 * @since   1.0
	 */
	public function getIgnoredSearchWords()
	{
		if ($this->ignoredSearchWordsCallback !== null)
		{
			return call_user_func($this->ignoredSearchWordsCallback);
		}

		return array();
	}

	/**
	 * Getter for ignoredSearchWordsCallback function.
	 *
	 * @return  callable  Function name or the actual function.
	 *
	 * @since   1.0
	 */
	public function getIgnoredSearchWordsCallback()
	{
		return $this->ignoredSearchWordsCallback;
	}

	/**
	 * Setter for the ignoredSearchWordsCallback function
	 *
	 * @param   callable  $function  Function name or actual function.
	 *
	 * @return  callable  The previous function.
	 *
	 * @since   1.0
	 */
	public function setIgnoredSearchWordsCallback($function)
	{
		$previous = $this->ignoredSearchWordsCallback;
		$this->ignoredSearchWordsCallback = $function;

		return $previous;
	}

	/**
	 * Returns a lower limit integer for length of search words
	 *
	 * @return  integer  The lower limit integer for length of search words (3 if no value was set for a specific language).
	 *
	 * @since   1.0
	 */
	public function getLowerLimitSearchWord()
	{
		if ($this->lowerLimitSearchWordCallback !== null)
		{
			return call_user_func($this->lowerLimitSearchWordCallback);
		}

		return 3;
	}

	/**
	 * Getter for lowerLimitSearchWordCallback function
	 *
	 * @return  callable  Function name or the actual function.
	 *
	 * @since   1.0
	 */
	public function getLowerLimitSearchWordCallback()
	{
		return $this->lowerLimitSearchWordCallback;
	}

	/**
	 * Setter for the lowerLimitSearchWordCallback function.
	 *
	 * @param   callable  $function  Function name or actual function.
	 *
	 * @return  callable  The previous function.
	 *
	 * @since   1.0
	 */
	public function setLowerLimitSearchWordCallback($function)
	{
		$previous = $this->lowerLimitSearchWordCallback;
		$this->lowerLimitSearchWordCallback = $function;

		return $previous;
	}

	/**
	 * Returns an upper limit integer for length of search words
	 *
	 * @return  integer  The upper limit integer for length of search words (20 if no value was set for a specific language).
	 *
	 * @since   1.0
	 */
	public function getUpperLimitSearchWord()
	{
		if ($this->upperLimitSearchWordCallback !== null)
		{
			return call_user_func($this->upperLimitSearchWordCallback);
		}

		return 20;
	}

	/**
	 * Getter for upperLimitSearchWordCallback function
	 *
	 * @return  callable  Function name or the actual function.
	 *
	 * @since   1.0
	 */
	public function getUpperLimitSearchWordCallback()
	{
		return $this->upperLimitSearchWordCallback;
	}

	/**
	 * Setter for the upperLimitSearchWordCallback function
	 *
	 * @param   callable  $function  Function name or the actual function.
	 *
	 * @return  callable  The previous function.
	 *
	 * @since   1.0
	 */
	public function setUpperLimitSearchWordCallback($function)
	{
		$previous = $this->upperLimitSearchWordCallback;
		$this->upperLimitSearchWordCallback = $function;

		return $previous;
	}

	/**
	 * Returns the number of characters displayed in search results.
	 *
	 * @return  integer  The number of characters displayed (200 if no value was set for a specific language).
	 *
	 * @since   1.0
	 */
	public function getSearchDisplayedCharactersNumber()
	{
		if ($this->searchDisplayedCharactersNumberCallback !== null)
		{
			return call_user_func($this->searchDisplayedCharactersNumberCallback);
		}

		return 200;
	}

	/**
	 * Getter for searchDisplayedCharactersNumberCallback function
	 *
	 * @return  callable  Function name or the actual function.
	 *
	 * @since   1.0
	 */
	public function getSearchDisplayedCharactersNumberCallback()
	{
		return $this->searchDisplayedCharactersNumberCallback;
	}

	/**
	 * Setter for the searchDisplayedCharactersNumberCallback function.
	 *
	 * @param   callable  $function  Function name or the actual function.
	 *
	 * @return  callable  The previous function.
	 *
	 * @since   1.0
	 */
	public function setSearchDisplayedCharactersNumberCallback($function)
	{
		$previous = $this->searchDisplayedCharactersNumberCallback;
		$this->searchDisplayedCharactersNumberCallback = $function;

		return $previous;
	}

	/**
	 * Checks if a language exists.
	 *
	 * This is a simple, quick check for the directory that should contain language files for the given user.
	 *
	 * @param   string  $lang      Language to check.
	 * @param   string  $basePath  Optional path to check.
	 *
	 * @return  boolean  True if the language exists.
	 *
	 * @see     LanguageHelper::exists()
	 * @since   1.0
	 * @deprecated  3.0  Use LanguageHelper::exists() instead
	 */
	public static function exists($lang, $basePath = '')
	{
		$helper = new LanguageHelper;

		return $helper->exists($lang, $basePath);
	}

	/**
	 * Loads a single language file and appends the results to the existing strings
	 *
	 * @param   string   $extension  The extension for which a language file should be loaded.
	 * @param   string   $basePath   The basepath to use.
	 * @param   string   $lang       The language to load, default null for the current language.
	 * @param   boolean  $reload     Flag that will force a language to be reloaded if set to true.
	 * @param   boolean  $default    Flag that force the default language to be loaded if the current does not exist.
	 *
	 * @return  boolean  True if the file has successfully loaded.
	 *
	 * @since   1.0
	 */
	public function load($extension = 'joomla', $basePath = '', $lang = null, $reload = false, $default = true)
	{
		$lang     = !$lang ? $this->lang : $lang;
		$basePath = empty($basePath) ? $this->basePath : $basePath;

		$path = $this->helper->getLanguagePath($basePath, $lang);

		$internal = $extension == 'joomla' || $extension == '';
		$filename = $internal ? $lang : $lang . '.' . $extension;
		$filename = "$path/$filename.ini";

		if (isset($this->paths[$extension][$filename]) && !$reload)
		{
			// This file has already been tested for loading.
			return $this->paths[$extension][$filename];
		}

		// Load the language file
		$result = $this->loadLanguage($filename, $extension);

		// Check whether there was a problem with loading the file
		if ($result === false && $default)
		{
			// No strings, so either file doesn't exist or the file is invalid
			$oldFilename = $filename;

			// Check the standard file name
			$path = $this->helper->getLanguagePath($basePath, $this->default);
			$filename = $internal ? $this->default : $this->default . '.' . $extension;
			$filename = "$path/$filename.ini";

			// If the one we tried is different than the new name, try again
			if ($oldFilename != $filename)
			{
				$result = $this->loadLanguage($filename, $extension);
			}
		}

		return $result;
	}

	/**
	 * Loads a language file.
	 *
	 * This method will not note the successful loading of a file - use load() instead.
	 *
	 * @param   string  $filename   The name of the file.
	 * @param   string  $extension  The name of the extension.
	 *
	 * @return  boolean  True if new strings have been added to the language
	 *
	 * @see     Language::load()
	 * @since   1.0
	 */
	protected function loadLanguage($filename, $extension = 'unknown')
	{
		$this->counter++;

		$result = false;
		$strings = false;

		if (file_exists($filename))
		{
			$strings = $this->parse($filename);
		}

		if ($strings)
		{
			if (is_array($strings))
			{
				// Sort the underlying heap by key values to optimize merging
				ksort($strings, SORT_STRING);
				$this->strings = array_merge($this->strings, $strings);
			}

			if (is_array($strings) && count($strings))
			{
				// Do not bother with ksort here.  Since the originals were sorted, PHP will already have chosen the best heap.
				$this->strings = array_merge($this->strings, $this->override);
				$result = true;
			}
		}

		// Record the result of loading the extension's file.
		if (!isset($this->paths[$extension]))
		{
			$this->paths[$extension] = array();
		}

		$this->paths[$extension][$filename] = $result;

		return $result;
	}

	/**
	 * Parses a language file.
	 *
	 * @param   string  $filename  The name of the file.
	 *
	 * @return  array  The array of parsed strings.
	 *
	 * @since   1.0
	 */
	protected function parse($filename)
	{
		if ($this->debug)
		{
			// Capture hidden PHP errors from the parsing.
			$php_errormsg = null;
			$track_errors = ini_get('track_errors');
			ini_set('track_errors', true);
		}

		$strings = @parse_ini_file($filename);

		if (!is_array($strings))
		{
			$strings = array();
		}

		if ($this->debug)
		{
			// Restore error tracking to what it was before.
			ini_set('track_errors', $track_errors);

			// Initialise variables for manually parsing the file for common errors.
			$blacklist = array('YES', 'NO', 'NULL', 'FALSE', 'ON', 'OFF', 'NONE', 'TRUE');
			$this->debug = false;
			$errors = array();

			// Open the file as a stream.
			$file = new \SplFileObject($filename);

			foreach ($file as $lineNumber => $line)
			{
				// Avoid BOM error as BOM is OK when using parse_ini.
				if ($lineNumber == 0)
				{
					$line = str_replace("\xEF\xBB\xBF", '', $line);
				}

				$line = trim($line);

				// Ignore comment lines.
				if (!strlen($line) || $line['0'] == ';')
				{
					continue;
				}

				// Ignore grouping tag lines, like: [group]
				if (preg_match('#^\[[^\]]*\](\s*;.*)?$#', $line))
				{
					continue;
				}

				$realNumber = $lineNumber + 1;

				// Check for any incorrect uses of _QQ_.
				if (strpos($line, '_QQ_') !== false)
				{
					$errors[] = $realNumber;
					continue;
				}

				// Check for odd number of double quotes.
				if (substr_count($line, '"') % 2 != 0)
				{
					$errors[] = $realNumber;
					continue;
				}

				// Check that the line passes the necessary format.
				if (!preg_match('#^[A-Z][A-Z0-9_\-\.]*\s*=\s*".*"(\s*;.*)?$#', $line))
				{
					$errors[] = $realNumber;
					continue;
				}

				// Check that the key is not in the blacklist.
				$key = strtoupper(trim(substr($line, 0, strpos($line, '='))));

				if (in_array($key, $blacklist))
				{
					$errors[] = $realNumber;
				}
			}

			// Check if we encountered any errors.
			if (count($errors))
			{
				$this->errorfiles[$filename] = $filename . ' - error(s) in line(s) ' . implode(', ', $errors);
			}
			elseif ($php_errormsg)
			{
				// We didn't find any errors but there's probably a parse notice.
				$this->errorfiles['PHP' . $filename] = 'PHP parser errors -' . $php_errormsg;
			}

			$this->debug = true;
		}

		return $strings;
	}

	/**
	 * Get a metadata language property.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 *
	 * @return  mixed  The value of the property.
	 *
	 * @since   1.0
	 */
	public function get($property, $default = null)
	{
		if (isset($this->metadata[$property]))
		{
			return $this->metadata[$property];
		}

		return $default;
	}

	/**
	 * Determine who called Language or Text.
	 *
	 * @return  array  Caller information.
	 *
	 * @since   1.0
	 */
	protected function getCallerInfo()
	{
		// Try to determine the source if none was provided
		// @codeCoverageIgnoreStart
		if (!function_exists('debug_backtrace'))
		{
			return null;
		}

		// @codeCoverageIgnoreEnd
		$backtrace = debug_backtrace();
		$info = array();

		// Search through the backtrace to our caller
		$continue = true;

		while ($continue && next($backtrace))
		{
			$step = current($backtrace);
			$class = @ $step['class'];

			// We're looking for something outside of language.php
			if ($class != '\\Joomla\\Language\\Language' && $class != '\\Joomla\\Language\\Text')
			{
				$info['function'] = @ $step['function'];
				$info['class'] = $class;
				$info['step'] = prev($backtrace);

				// Determine the file and name of the file
				$info['file'] = @ $step['file'];
				$info['line'] = @ $step['line'];

				$continue = false;
			}
		}

		return $info;
	}

	/**
	 * Getter for Name.
	 *
	 * @return  string  Official name element of the language.
	 *
	 * @since   1.0
	 */
	public function getName()
	{
		return $this->metadata['name'];
	}

	/**
	 * Get a list of language files that have been loaded.
	 *
	 * @param   string  $extension  An optional extension name.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getPaths($extension = null)
	{
		if (isset($extension))
		{
			if (isset($this->paths[$extension]))
			{
				return $this->paths[$extension];
			}

			return null;
		}

		return $this->paths;
	}

	/**
	 * Get a list of language files that are in error state.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getErrorFiles()
	{
		return $this->errorfiles;
	}

	/**
	 * Getter for the language tag (as defined in RFC 3066)
	 *
	 * @return  string  The language tag.
	 *
	 * @since   1.0
	 */
	public function getTag()
	{
		return $this->metadata['tag'];
	}

	/**
	 * Get the RTL property.
	 *
	 * @return  boolean  True is it an RTL language.
	 *
	 * @since   1.0
	 */
	public function isRTL()
	{
		return (bool) $this->metadata['rtl'];
	}

	/**
	 * Set the Debug property.
	 *
	 * @param   boolean  $debug  The debug setting.
	 *
	 * @return  boolean  Previous value.
	 *
	 * @since   1.0
	 */
	public function setDebug($debug)
	{
		$previous = $this->debug;
		$this->debug = (boolean) $debug;

		return $previous;
	}

	/**
	 * Get the Debug property.
	 *
	 * @return  boolean  True is in debug mode.
	 *
	 * @since   1.0
	 */
	public function getDebug()
	{
		return $this->debug;
	}

	/**
	 * Get the default language code.
	 *
	 * @return  string  Language code.
	 *
	 * @since   1.0
	 */
	public function getDefault()
	{
		return $this->default;
	}

	/**
	 * Set the default language code.
	 *
	 * @param   string  $lang  The language code.
	 *
	 * @return  string  Previous value.
	 *
	 * @since   1.0
	 */
	public function setDefault($lang)
	{
		$previous = $this->default;
		$this->default = $lang;

		return $previous;
	}

	/**
	 * Get the list of orphaned strings if being tracked.
	 *
	 * @return  array  Orphaned text.
	 *
	 * @since   1.0
	 */
	public function getOrphans()
	{
		return $this->orphans;
	}

	/**
	 * Get the list of used strings.
	 *
	 * Used strings are those strings requested and found either as a string or a constant.
	 *
	 * @return  array  Used strings.
	 *
	 * @since   1.0
	 */
	public function getUsed()
	{
		return $this->used;
	}

	/**
	 * Determines is a key exists.
	 *
	 * @param   string  $string  The key to check.
	 *
	 * @return  boolean  True, if the key exists.
	 *
	 * @since   1.0
	 */
	public function hasKey($string)
	{
		$key = strtoupper($string);

		return isset($this->strings[$key]);
	}

	/**
	 * Returns a associative array holding the metadata.
	 *
	 * @param   string  $lang      The name of the language.
	 * @param   string  $basePath  The filepath to the language folder.
	 *
	 * @return  mixed  If $lang exists return key/value pair with the language metadata, otherwise return NULL.
	 *
	 * @see     LanguageHelper::getMetadata()
	 * @since   1.0
	 * @deprecated  3.0  Use LanguageHelper::getMetadata() instead
	 */
	public static function getMetadata($lang, $basePath)
	{
		$helper = new LanguageHelper;

		return $helper->getMetadata($lang, $basePath);
	}

	/**
	 * Returns a list of known languages for an area
	 *
	 * @param   string  $basePath  The basepath to use
	 *
	 * @return  array  key/value pair with the language file and real name.
	 *
	 * @see     LanguageHelper::getKnownLanguages()
	 * @since   1.0
	 * @deprecated  3.0  Use LanguageHelper::getKnownLanguages() instead
	 */
	public static function getKnownLanguages($basePath = '')
	{
		$helper = new LanguageHelper;

		return $helper->getKnownLanguages($basePath);
	}

	/**
	 * Get the path to a language
	 *
	 * @param   string  $basePath  The basepath to use.
	 * @param   string  $language  The language tag.
	 *
	 * @return  string  language related path or null.
	 *
	 * @see     LanguageHelper::getLanguagePath()
	 * @since   1.0
	 * @deprecated  3.0  Use LanguageHelper::getLanguagePath() instead
	 */
	public static function getLanguagePath($basePath = '', $language = null)
	{
		$helper = new LanguageHelper;

		return $helper->getLanguagePath($basePath, $language);
	}

	/**
	 * Get the current language code.
	 *
	 * @return  string  The language code
	 *
	 * @since   1.0
	 */
	public function getLanguage()
	{
		return $this->lang;
	}

	/**
	 * Set the language attributes to the given language.
	 *
	 * Once called, the language still needs to be loaded using Language::load().
	 *
	 * @param   string  $lang  Language code.
	 *
	 * @return  string  Previous value.
	 *
	 * @since   1.0
	 * @deprecated  2.0  Instantiate a new Language object in the new language instead.
	 */
	public function setLanguage($lang)
	{
		$previous = $this->lang;
		$this->lang = $lang;
		$this->metadata = $this->helper->getMetadata($this->lang, $this->basePath);

		return $previous;
	}

	/**
	 * Get the language locale based on current language.
	 *
	 * @return  array  The locale according to the language.
	 *
	 * @since   1.0
	 */
	public function getLocale()
	{
		if (!isset($this->locale))
		{
			$locale = str_replace(' ', '', isset($this->metadata['locale']) ? $this->metadata['locale'] : '');

			if ($locale)
			{
				$this->locale = explode(',', $locale);
			}
			else
			{
				$this->locale = false;
			}
		}

		return $this->locale;
	}

	/**
	 * Retrieves a new Text object for the current instance
	 *
	 * @return  Text
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getText()
	{
		return new Text($this);
	}

	/**
	 * Get the first day of the week for this language.
	 *
	 * @return  integer  The first day of the week according to the language
	 *
	 * @since   1.0
	 */
	public function getFirstDay()
	{
		return (int) (isset($this->metadata['firstDay']) ? $this->metadata['firstDay'] : 0);
	}

	/**
	 * Searches for language directories within a certain base dir.
	 *
	 * @param   string  $dir  directory of files.
	 *
	 * @return  array  Array holding the found languages as filename => real name pairs.
	 *
	 * @see     LanguageHelper::parseLanguageFiles()
	 * @since   1.0
	 * @deprecated  3.0  Use LanguageHelper::parseLanguageFiles() instead
	 */
	public static function parseLanguageFiles($dir = null)
	{
		$helper = new LanguageHelper;

		return $helper->parseLanguageFiles($dir);
	}

	/**
	 * Parse XML file for language information.
	 *
	 * @param   string  $path  Path to the XML files.
	 *
	 * @return  array  Array holding the found metadata as a key => value pair.
	 *
	 * @see     LanguageHelper::parseXMLLanguageFile()
	 * @since   1.0
	 * @deprecated  3.0  Use LanguageHelper::parseXMLLanguageFile() instead
	 */
	public static function parseXMLLanguageFile($path)
	{
		$helper = new LanguageHelper;

		return $helper->parseXMLLanguageFile($path);
	}
}

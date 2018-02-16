<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Languages/translation handler class
 *
 * @since  1.0
 */
class Language
{
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
	 * The localisation object.
	 *
	 * @var    LocaliseInterface
	 * @since  __DEPLOY_VERSION__
	 */
	protected $localise = null;

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
	 * MessageCatalogue object
	 *
	 * @var    MessageCatalogue
	 * @since  __DEPLOY_VERSION__
	 */
	protected $catalogue;

	/**
	 * Constructor activating the default information of the language.
	 *
	 * @param   string   $path   The base path to the language folder
	 * @param   string   $lang   The language
	 * @param   boolean  $debug  Indicates if language debugging is enabled.
	 *
	 * @since   1.0
	 */
	public function __construct($path, $lang = '', $debug = false)
	{
		if (empty($path))
		{
			throw new \InvalidArgumentException(
				'The $path variable cannot be empty when creating a new Language object'
			);
		}

		$this->basePath = $path;
		$this->helper   = new LanguageHelper;

		$this->lang = $lang ?: $this->default;

		$this->metadata = $this->helper->getMetadata($this->lang, $this->basePath);
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

		// Grab a localisation file
		$this->localise = (new LanguageFactory)->getLocalise($lang, $path);

		$this->catalogue = new MessageCatalogue($this->lang);

		$this->load();
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
		@trigger_error(
			sprintf(
				'%1$s() is deprecated and will be removed in 3.0, use %2$s::translate() instead.',
				__METHOD__,
				Language::class
			),
			E_USER_DEPRECATED
		);

		return $this->translate((string) $string, (bool) $jsSafe, (bool) $interpretBackSlashes);
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
	public function translate(string $string, bool $jsSafe = false, bool $interpretBackSlashes = true): string
	{
		// Detect empty string
		if ($string == '')
		{
			return '';
		}

		$key = strtoupper($string);

		if ($this->catalogue->hasMessage($key))
		{
			$string = $this->debug ? '**' . $this->catalogue->getMessage($key) . '**' : $this->catalogue->getMessage($key);

			// Store debug information
			if ($this->debug)
			{
				$caller = $this->getCallerInfo();

				if (!array_key_exists($key, $this->used))
				{
					$this->used[$key] = [];
				}

				$this->used[$key][] = $caller;
			}
		}
		else
		{
			if ($this->debug)
			{
				$caller           = $this->getCallerInfo();
				$caller['string'] = $string;

				if (!array_key_exists($key, $this->orphans))
				{
					$this->orphans[$key] = [];
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
			if (strpos($string, '\\') !== false)
			{
				// Interpret \n and \t characters
				$string = str_replace(['\\\\', '\t', '\n'], ["\\", "\t", "\n"], $string);
			}
		}

		return $string;
	}

	/**
	 * Transliterate function
	 *
	 * This method processes a string and replaces all accented UTF-8 characters by unaccented ASCII-7 "equivalents".
	 *
	 * @param   string  $string  The string to transliterate.
	 *
	 * @return  string  The transliteration of the string.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function transliterate($string)
	{
		$string = $this->localise->transliterate($string);

		// The transliterate method can return false if there isn't a fully valid UTF-8 string entered
		if ($string === false)
		{
			throw new \RuntimeException('Invalid UTF-8 was detected in the string "%s"', $lowercaseString);
		}

		return $string;
	}

	/**
	 * Returns an array of suffixes for plural rules.
	 *
	 * @param   integer  $count  The count number the rule is for.
	 *
	 * @return  string[]  The array of suffixes.
	 *
	 * @since   1.0
	 */
	public function getPluralSuffixes($count)
	{
		return $this->localise->getPluralSuffixes($count);
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
		@trigger_error(
			sprintf(
				'%1$s() is deprecated and will be removed in 3.0, use %2$s::exists() instead.',
				__METHOD__,
				LanguageHelper::class
			),
			E_USER_DEPRECATED
		);

		return (new LanguageHelper)->exists($lang, $basePath);
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
		$lang     = $lang ?: $this->lang;
		$basePath = $basePath ?: $this->basePath;

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
			$path     = $this->helper->getLanguagePath($basePath, $this->default);
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

		$result  = false;
		$strings = false;

		if (file_exists($filename))
		{
			$strings = $this->parse($filename);
		}

		if ($strings)
		{
			if (is_array($strings) && count($strings))
			{
				$this->catalogue->addMessages(array_replace($strings, $this->override));
				$result = true;
			}
		}

		// Record the result of loading the extension's file.
		if (!isset($this->paths[$extension]))
		{
			$this->paths[$extension] = [];
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
		// Capture hidden PHP errors from the parsing.
		if ($this->debug)
		{
			// See https://secure.php.net/manual/en/reserved.variables.phperrormsg.php
			$php_errormsg = null;
			$trackErrors  = ini_get('track_errors');
			ini_set('track_errors', true);
		}

		$strings = @parse_ini_file($filename);

		if ($this->debug)
		{
			// Restore error tracking to what it was before.
			ini_set('track_errors', $trackErrors);

			$this->debugFile($filename);
		}

		return is_array($strings) ? $strings : [];
	}

	/**
	 * Debugs a language file
	 *
	 * @param   string  $filename  Absolute path to the file to debug
	 *
	 * @return  integer  A count of the number of parsing errors
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function debugFile(string $filename): int
	{
		// Make sure our file actually exists
		if (!file_exists($filename))
		{
			throw new \InvalidArgumentException(
				sprintf('Unable to locate file "%s" for debugging', $filename)
			);
		}

		// Initialise variables for manually parsing the file for common errors.
		$blacklist    = ['YES', 'NO', 'NULL', 'FALSE', 'ON', 'OFF', 'NONE', 'TRUE'];
		$debug        = $this->setDebug(false);
		$errors       = [];
		$php_errormsg = null;

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
			if (!preg_match('#^[A-Z][A-Z0-9_\*\-\.]*\s*=\s*".*"(\s*;.*)?$#', $line))
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

		$this->setDebug($debug);

		return count($errors);
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
		return $this->metadata[$property] ?? $default;
	}

	/**
	 * Get the base path for the instance.
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getBasePath(): string
	{
		return $this->basePath;
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
		$info      = [];

		// Search through the backtrace to our caller
		$continue = true;

		while ($continue && next($backtrace))
		{
			$step  = current($backtrace);
			$class = @ $step['class'];

			// We're looking for something outside of language.php
			if ($class != __CLASS__ && $class != Text::class)
			{
				$info['function'] = @ $step['function'];
				$info['class']    = $class;
				$info['step']     = prev($backtrace);

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
			return $this->paths[$extension] ?? null;
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
	public function isRtl()
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
		$previous    = $this->debug;
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
		return $this->catalogue->hasMessage($string);
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
		@trigger_error(
			sprintf(
				'%1$s() is deprecated and will be removed in 3.0, use %2$s::getMetadata() instead.',
				__METHOD__,
				LanguageHelper::class
			),
			E_USER_DEPRECATED
		);

		return (new LanguageHelper)->getMetadata($lang, $basePath);
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
		@trigger_error(
			sprintf(
				'%1$s() is deprecated and will be removed in 3.0, use %2$s::getKnownLanguages() instead.',
				__METHOD__,
				LanguageHelper::class
			),
			E_USER_DEPRECATED
		);

		return (new LanguageHelper)->getKnownLanguages($basePath);
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
	public static function getLanguagePath($basePath = '', $language = '')
	{
		@trigger_error(
			sprintf(
				'%1$s() is deprecated and will be removed in 3.0, use %2$s::getLanguagePath() instead.',
				__METHOD__,
				LanguageHelper::class
			),
			E_USER_DEPRECATED
		);

		return (new LanguageHelper)->getLanguagePath($basePath, $language);
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
	 * Get the message catalogue for the language.
	 *
	 * @return  MessageCatalogue
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getCatalogue(): MessageCatalogue
	{
		return $this->catalogue;
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
			$locale = str_replace(' ', '', $this->metadata['locale'] ?? '');

			$this->locale = $locale ? explode(',', $locale) : false;
		}

		return $this->locale;
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
		return (int) ($this->metadata['firstDay'] ?? 0);
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
		@trigger_error(
			sprintf(
				'%1$s() is deprecated and will be removed in 3.0, use %2$s::parseLanguageFiles() instead.',
				__METHOD__,
				LanguageHelper::class
			),
			E_USER_DEPRECATED
		);

		return (new LanguageHelper)->parseLanguageFiles($dir);
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
	public static function parseXmlLanguageFile($path)
	{
		@trigger_error(
			sprintf(
				'%1$s() is deprecated and will be removed in 3.0, use %2$s::parseXMLLanguageFile() instead.',
				__METHOD__,
				LanguageHelper::class
			),
			E_USER_DEPRECATED
		);

		return (new LanguageHelper)->parseXMLLanguageFile($path);
	}
}

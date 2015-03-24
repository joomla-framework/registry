<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

use Joomla\Language\Localise\En_GBLocalise as DefaultLocalise;

/**
 * Language package factory
 *
 * @since  __DEPLOY_VERSION__
 */
class LanguageFactory
{
	/**
	 * Application's default language
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $defaultLanguage = 'en-GB';

	/**
	 * Path to the directory containing the application's language folder
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $languageDirectory;

	/**
	 * Container with a list of loaded classes grouped by object type
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	private static $loadedClasses = array(
		'language' => array(),
		'localise' => array(),
		'stemmer'  => array()
	);

	/**
	 * Get the application's default language
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getDefaultLanguage()
	{
		return $this->defaultLanguage;
	}

	/**
	 * Returns a language object.
	 *
	 * @param   string   $lang   The language to use.
	 * @param   string   $path   The base path to the language folder.  This is required if creating a new instance.
	 * @param   boolean  $debug  The debug mode.
	 *
	 * @return  Language
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getLanguage($lang = null, $path = null, $debug = false)
	{
		$path = ($path === null) ? $this->getLanguageDirectory() : $path;
		$lang = ($lang === null) ? $this->getDefaultLanguage() : $lang;

		if (!isset(self::$loadedClasses['language'][$lang]))
		{
			self::$loadedClasses['language'][$lang] = new Language($path, $lang, $debug);
		}

		return self::$loadedClasses['language'][$lang];
	}

	/**
	 * Get the path to the directory containing the application's language folder
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getLanguageDirectory()
	{
		return $this->languageDirectory;
	}

	/**
	 * Searches for a specific localise file for a given language. Falls back to the .
	 *
	 * @param   string  $lang      Language to check.
	 * @param   string  $basePath  Base path to the language folder.
	 *
	 * @return  LocaliseInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getLocalise($lang, $basePath = null)
	{
		/*
		 * Look for a language specific localise class
		 *
		 * LocaliseInterface classes are searched for in the global namespace and are named based
		 * on the language code, replacing hyphens with underscores (i.e. en-GB looks for En_GBLocalise)
		 */
		$class = str_replace('-', '_', $lang . 'Localise');

		// If we've already found this object, no need to try and find it again
		if (isset(self::$loadedClasses['localise'][$class]))
		{
			return new $class;
		}

		$paths = array();

		$basePath = ($basePath === null) ? $this->getLanguageDirectory() : $basePath;

		// Get the LanguageHelper to set the proper language directory
		$languageHelper = new LanguageHelper;

		$basePath = $languageHelper->getLanguagePath($basePath);

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

		// If we have found a match initialise it and return it
		if (class_exists($class))
		{
			// Need to instantiate the class to check it implements the LocaliseInterface
			$localiseObject = new $class;

			if (!($localiseObject instanceof LocaliseInterface))
			{
				throw new \RuntimeException(
					sprintf(
						'The %s class must implement the LocaliseInterface.',
						$class
					)
				);
			}

			// Store the class name to the cache
			self::$loadedClasses['localise'][$class] = true;

			return $localiseObject;
		}

		// Return the en_GB class if no specific instance is found
		return new DefaultLocalise;
	}

	/**
	 * Method to get a stemmer, creating it if necessary.
	 *
	 * @param   string  $adapter  The type of stemmer to load.
	 *
	 * @return  Stemmer
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  RuntimeException on invalid stemmer
	 */
	public function getStemmer($adapter)
	{
		// Setup the adapter for the stemmer.
		$class = '\\Joomla\\Language\\Stemmer\\' . ucfirst(trim($adapter));

		// If we've already found this object, no need to try and find it again
		if (isset(self::$loadedClasses['stemmer'][$class]))
		{
			return self::$loadedClasses['stemmer'][$class];
		}

		// Check if a stemmer exists for the adapter.
		if (!class_exists($class))
		{
			// Throw invalid adapter exception.
			throw new \RuntimeException(sprintf('Invalid stemmer type %s', $class));
		}

		$stemmer = new $class;

		if (!($stemmer instanceof Stemmer))
		{
			throw new \RuntimeException(
				sprintf(
					'The %s class must extend the Stemmer class.',
					$class
				)
			);
		}

		// Store the class name to the cache
		self::$loadedClasses['stemmer'][$class] = $stemmer;

		return $stemmer;
	}

	/**
	 * Retrieves a new Text object for a Language instance
	 *
	 * @param   Language  $language  An optional Language object to inject, otherwise the default object is loaded
	 *
	 * @return  Text
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getText(Language $language = null)
	{
		$language = $language === null ? $this->getLanguage() : $language;

		return new Text($language);
	}

	/**
	 * Set the application's default language
	 *
	 * @param   string  $language  Language code for the application's default language
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setDefaultLanguage($language)
	{
		$this->defaultLanguage = $language;

		return $this;
	}

	/**
	 * Set the path to the directory containing the application's language folder
	 *
	 * @param   string  $directory  Path to the application's language folder
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setLanguageDirectory($directory)
	{
		if (!is_dir($directory))
		{
			throw new \InvalidArgumentException(
				sprintf(
					'Cannot set language directory to "%s" since the directory does not exist.',
					$directory
				)
			);
		}

		$this->languageDirectory = $directory;

		return $this;
	}
}

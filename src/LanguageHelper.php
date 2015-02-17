<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Helper class for the Language package
 *
 * @since  __DEPLOY_VERSION__
 */
class LanguageHelper
{
	/**
	 * Checks if a language exists.
	 *
	 * This is a simple, quick check for the directory that should contain language files for the given user.
	 *
	 * @param   string  $lang      Language to check.
	 * @param   string  $basePath  Directory to check for the specified language.
	 *
	 * @return  boolean  True if the language exists.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function exists($lang, $basePath)
	{
		return is_dir($this->getLanguagePath($basePath, $lang));
	}

	/**
	 * Returns a associative array holding the metadata.
	 *
	 * @param   string  $lang  The name of the language.
	 * @param   string  $path  The filepath to the language folder.
	 *
	 * @return  array|null  If $lang exists return key/value pair with the language metadata, otherwise return NULL.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getMetadata($lang, $path)
	{
		$path = $this->getLanguagePath($path, $lang);
		$file = $lang . '.xml';

		$result = null;

		if (is_file("$path/$file"))
		{
			$result = $this->parseXMLLanguageFile("$path/$file");
		}

		if (empty($result))
		{
			return null;
		}

		return $result;
	}

	/**
	 * Returns a list of known languages for an area
	 *
	 * @param   string  $basePath  The basepath to use
	 *
	 * @return  array  key/value pair with the language file and real name.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getKnownLanguages($basePath)
	{
		$dir = $this->getLanguagePath($basePath);
		$knownLanguages = $this->parseLanguageFiles($dir);

		return $knownLanguages;
	}

	/**
	 * Get the path to a language
	 *
	 * @param   string  $basePath  The basepath to use.
	 * @param   string  $language  The language tag.
	 *
	 * @return  string  Path to the language folder
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getLanguagePath($basePath, $language = null)
	{
		$dir = $basePath . '/language';

		if (!empty($language))
		{
			$dir .= '/' . $language;
		}

		return $dir;
	}

	/**
	 * Searches for language directories within a certain base dir.
	 *
	 * @param   string  $dir  directory of files.
	 *
	 * @return  array  Array holding the found languages as filename => real name pairs.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function parseLanguageFiles($dir = null)
	{
		$languages = array();

		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));

		foreach ($iterator as $file)
		{
			$langs    = array();
			$fileName = $file->getFilename();

			if (!$file->isFile() || !preg_match("/^([-_A-Za-z]*)\.xml$/", $fileName))
			{
				continue;
			}

			try
			{
				$metadata = $this->parseXMLLanguageFile($file->getRealPath());

				if ($metadata)
				{
					$lang = str_replace('.xml', '', $fileName);
					$langs[$lang] = $metadata;
				}

				$languages = array_merge($languages, $langs);
			}
			catch (\RuntimeException $e)
			{
			}
		}

		return $languages;
	}

	/**
	 * Parse XML file for language information.
	 *
	 * @param   string  $path  Path to the XML files.
	 *
	 * @return  array  Array holding the found metadata as a key => value pair.
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	public function parseXMLLanguageFile($path)
	{
		if (!is_readable($path))
		{
			throw new \RuntimeException('File not found or not readable');
		}

		// Try to load the file
		$xml = simplexml_load_file($path);

		if (!$xml)
		{
			return null;
		}

		// Check that it's a metadata file
		if ((string) $xml->getName() != 'metafile')
		{
			return null;
		}

		$metadata = array();

		foreach ($xml->metadata->children() as $child)
		{
			$metadata[$child->getName()] = (string) $child;
		}

		return $metadata;
	}
}

<?php
/**
 * Part of the Joomla Framework Keychain Package
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain;

use Joomla\Crypt\Crypt;
use Joomla\Registry\Registry;

/**
 * Keychain Class
 *
 * @since  1.0
 */
class Keychain extends Registry
{
	/**
	 * The encryption handler.
	 *
	 * @var    Crypt
	 * @since  __DEPLOY_VERSION__
	 */
	protected $crypt;

	/**
	 * Constructor
	 *
	 * @param   Crypt  $crypt  The encryption handler.
	 * @param   mixed  $data   The data to bind to the new Registry object.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(Crypt $crypt, $data = null)
	{
		parent::__construct($data);

		$this->crypt = $crypt;
	}

	/**
	 * Delete a registry value (very simple method)
	 *
	 * @param   string  $path  Registry Path (e.g. joomla.content.showauthor)
	 *
	 * @return  mixed  Value of old value or boolean false if operation failed
	 *
	 * @since   1.0
	 */
	public function deleteValue($path)
	{
		$result = null;

		// Explode the registry path into an array
		$nodes = explode('.', $path);

		if ($nodes)
		{
			// Initialize the current node to be the registry root.
			$node = $this->data;

			// Traverse the registry to find the correct node for the result.
			for ($i = 0, $n = count($nodes) - 1; $i < $n; $i++)
			{
				if (!isset($node->$nodes[$i]) && ($i != $n))
				{
					$node->{$nodes[$i]} = new \stdClass;
				}

				$node = $node->{$nodes[$i]};
			}

			// Get the old value if exists so we can return it
			$result = $node->{$nodes[$i]};
			unset($node->{$nodes[$i]});
		}

		return $result;
	}

	/**
	 * Load a keychain file into this object.
	 *
	 * @param   string  $keychainFile  Path to the keychain file.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function loadKeychain($keychainFile)
	{
		if (!file_exists($keychainFile))
		{
			throw new \RuntimeException('Attempting to load non-existent keychain file');
		}

		$cleartext = $this->crypt->decrypt(file_get_contents($keychainFile));

		return $this->loadObject(json_decode($cleartext));
	}

	/**
	 * Save this keychain to a file.
	 *
	 * @param   string  $keychainFile  The path to the keychain file.
	 *
	 * @return  boolean  Result of storing the file.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function saveKeychain($keychainFile)
	{
		if (empty($keychainFile))
		{
			throw new \RuntimeException('A keychain file must be specified');
		}

		$data = $this->toString('JSON');

		$encrypted = $this->crypt->encrypt($data);

		return file_put_contents($keychainFile, $encrypted);
	}
}

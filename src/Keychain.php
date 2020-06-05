<?php
/**
 * Part of the Joomla Framework Keychain Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain;

use Joomla\Crypt\Crypt;
use Joomla\Crypt\Exception\CryptExceptionInterface;
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
	 * @since  2.0.0-beta
	 */
	protected $crypt;

	/**
	 * Constructor
	 *
	 * @param   Crypt  $crypt  The encryption handler.
	 * @param   mixed  $data   The data to bind to the new Keychain.
	 *
	 * @since   2.0.0-beta
	 */
	public function __construct(Crypt $crypt, $data = null)
	{
		parent::__construct($data);

		$this->crypt = $crypt;
	}

	/**
	 * Load a keychain file into this object.
	 *
	 * @param   string  $keychainFile  Path to the keychain file.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \RuntimeException if the keychain file does not exist
	 * @throws  CryptExceptionInterface if the keychain cannot be decrypted
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
	 * @throws  \RuntimeException if the keychain file path is invalid
	 * @throws  CryptExceptionInterface if the keychain cannot be encrypted
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

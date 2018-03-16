<?php
/**
 * Part of the Joomla Framework Keychain Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Command;

use Joomla\Console\AbstractCommand;
use Joomla\Crypt\Crypt;
use Joomla\Keychain\Keychain;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Base class for all keychain console commands.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractKeychainCommand extends AbstractCommand
{
	/**
	 * The encryption handler.
	 *
	 * @var    Crypt
	 * @since  __DEPLOY_VERSION__
	 */
	protected $crypt;

	/**
	 * The keychain being managed.
	 *
	 * @var    Keychain
	 * @since  __DEPLOY_VERSION__
	 */
	protected $keychain;

	/**
	 * Constructor
	 *
	 * @param   Crypt  $crypt  The encryption handler for use within the Keychain.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(Crypt $crypt)
	{
		parent::__construct();

		$this->crypt    = $crypt;
		$this->keychain = new Keychain($this->crypt);
	}

	/**
	 * Initialise the command.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function initialise()
	{
		$this->addArgument(
			'filename',
			InputArgument::REQUIRED,
			'The path to the keychain file'
		);
	}

	/**
	 * Initialise the Keychain.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  InvalidArgumentException
	 */
	protected function initialiseKeychain()
	{
		$filename = $this->getApplication()->getConsoleInput()->getArgument('filename');

		if (!file_exists($filename))
		{
			throw new InvalidArgumentException(
				sprintf(
					'There is no readable file at `%s`.',
					$filename
				)
			);
		}

		$this->keychain->loadKeychain($filename);
	}

	/**
	 * Save the Keychain.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  RuntimeException
	 */
	protected function saveKeychain(): bool
	{
		$filename = $this->getApplication()->getConsoleInput()->getArgument('filename');

		if (!is_writable($filename))
		{
			throw new RuntimeException(
				sprintf(
					'Cannot write the keychain to `%s` as the path is not writable.',
					$filename
				)
			);
		}

		return $this->keychain->saveKeychain($filename);
	}
}

<?php
/**
 * Part of the Joomla Framework Keychain Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Command;

use Joomla\Console\Command\AbstractCommand;
use Joomla\Crypt\Crypt;
use Joomla\Keychain\Keychain;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for all keychain console commands.
 *
 * @since  2.0.0
 */
abstract class AbstractKeychainCommand extends AbstractCommand
{
	/**
	 * The encryption handler.
	 *
	 * @var    Crypt
	 * @since  2.0.0
	 */
	protected $crypt;

	/**
	 * The path to the keychain file.
	 *
	 * @var    string
	 * @since  2.0.0
	 */
	protected $filename;

	/**
	 * The keychain being managed.
	 *
	 * @var    Keychain
	 * @since  2.0.0
	 */
	protected $keychain;

	/**
	 * Constructor
	 *
	 * @param   Crypt  $crypt  The encryption handler for use within the Keychain.
	 *
	 * @since   2.0.0
	 */
	public function __construct(Crypt $crypt)
	{
		parent::__construct();

		$this->crypt    = $crypt;
		$this->keychain = new Keychain($this->crypt);
	}

	/**
	 * Configure the command.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	protected function configure(): void
	{
		$this->addArgument(
			'filename',
			InputArgument::REQUIRED,
			'The path to the keychain file'
		);
	}

	/**
	 * Internal hook to initialise the command after the input has been bound and before the input is validated.
	 *
	 * @param   InputInterface   $input   The input to inject into the command.
	 * @param   OutputInterface  $output  The output to inject into the command.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	protected function initialise(InputInterface $input, OutputInterface $output): void
	{
		$this->initialiseKeychain($input);
	}

	/**
	 * Initialise the Keychain.
	 *
	 * @param   InputInterface  $input  The input to inject into the command.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 * @throws  InvalidArgumentException
	 */
	protected function initialiseKeychain(InputInterface $input): void
	{
		$filename = $input->getArgument('filename');

		if (!file_exists($filename))
		{
			throw new InvalidArgumentException(
				sprintf(
					'There is no readable file at `%s`.',
					$filename
				)
			);
		}

		$this->filename = $filename;

		$this->keychain->loadKeychain($filename);
	}

	/**
	 * Save the Keychain.
	 *
	 * @return  boolean
	 *
	 * @since   2.0.0
	 * @throws  RuntimeException
	 */
	protected function saveKeychain(): bool
	{
		if (!is_writable($this->filename))
		{
			throw new RuntimeException(
				sprintf(
					'Cannot write the keychain to `%s` as the path is not writable.',
					$this->filename
				)
			);
		}

		return $this->keychain->saveKeychain($this->filename);
	}
}

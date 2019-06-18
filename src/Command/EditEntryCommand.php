<?php
/**
 * Part of the Joomla Framework Keychain Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command class to edit an entry in a keychain
 *
 * @since  __DEPLOY_VERSION__
 */
class EditEntryCommand extends AbstractKeychainCommand
{
	/**
	 * The default command name
	 *
	 * @var    string|null
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $defaultName = 'keychain:edit-entry';

	/**
	 * Internal function to execute the command.
	 *
	 * @param   InputInterface   $input   The input to inject into the command.
	 * @param   OutputInterface  $output  The output to inject into the command.
	 *
	 * @return  integer  The command exit code
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$symfonyStyle = new SymfonyStyle($input, $output);
		$symfonyStyle->title('Edit Keychain Entry');

		$entryName  = $input->getArgument('entry-name');
		$entryValue = $input->getArgument('entry-value');

		$this->keychain->set($entryName, $entryValue);

		if (!$this->saveKeychain())
		{
			$symfonyStyle->error('The entry was not edited in the keychain.');

			return 1;
		}

		$symfonyStyle->success('The entry was edited in the keychain.');

		return 0;
	}

	/**
	 * Configure the command.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function configure(): void
	{
		parent::configure();

		$this->setDescription('Edits an entry in the keychain');

		$this->addArgument(
			'entry-name',
			InputArgument::REQUIRED,
			'The key to use for the entry'
		);

		$this->addArgument(
			'entry-value',
			InputArgument::REQUIRED,
			'The value of the entry'
		);
	}
}

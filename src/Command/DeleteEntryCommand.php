<?php
/**
 * Part of the Joomla Framework Keychain Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * Command class to delete an entry in a keychain
 *
 * @since  __DEPLOY_VERSION__
 */
class DeleteEntryCommand extends AbstractKeychainCommand
{
	/**
	 * Execute the command.
	 *
	 * @return  integer  The exit code for the command.
	 */
	public function execute(): int
	{
		$symfonyStyle = $this->createSymfonyStyle();
		$symfonyStyle->title('Delete Keychain Entry');

		$this->initialiseKeychain();

		$entryName = $this->getApplication()->getConsoleInput()->getArgument('entry-name');

		if (!$this->keychain->exists($entryName))
		{
			$symfonyStyle->note(
				sprintf(
					'There is no entry in the keychain with the key `%s`.',
					$entryName
				)
			);

			return 0;
		}

		$this->keychain->deleteValue($entryName);

		if (!$this->saveKeychain())
		{
			$symfonyStyle->error('The entry was not removed from the keychain.');

			return 1;
		}

		$symfonyStyle->success('The entry was removed from the keychain.');

		return 0;
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
		parent::initialise();

		$this->setName('keychain:delete-entry');
		$this->setDescription('Deletes an entry in the keychain');

		$this->addArgument(
			'entry-name',
			InputArgument::REQUIRED,
			'The key to remove from the keychain'
		);
	}
}

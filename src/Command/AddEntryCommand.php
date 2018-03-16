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
 * Command class to add an entry to a keychain
 *
 * @since  __DEPLOY_VERSION__
 */
class AddEntryCommand extends AbstractKeychainCommand
{
	/**
	 * Execute the command.
	 *
	 * @return  integer  The exit code for the command.
	 */
	public function execute(): int
	{
		$symfonyStyle = $this->createSymfonyStyle();
		$symfonyStyle->title('Add Keychain Entry');

		$this->initialiseKeychain();

		$entryName  = $this->getApplication()->getConsoleInput()->getArgument('entry-name');
		$entryValue = $this->getApplication()->getConsoleInput()->getArgument('entry-value');

		if ($this->keychain->exists($entryName))
		{
			$symfonyStyle->warning(
				sprintf(
					'An entry already exists with the key `%s`, use the `keychain:edit-entry` command to edit it.',
					$entryName
				)
			);

			return 1;
		}

		$this->keychain->set($entryName, $entryValue);

		if (!$this->saveKeychain())
		{
			$symfonyStyle->error('The entry was not added to the keychain.');

			return 1;
		}

		$symfonyStyle->success('The entry was added to the keychain.');

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

		$this->setName('keychain:add-entry');
		$this->setDescription('Adds an entry to the keychain');

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

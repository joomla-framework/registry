<?php
/**
 * Part of the Joomla Framework Keychain Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Command;

use Symfony\Component\Console\Input\InputOption;

/**
 * Command class to list entries in a keychain
 *
 * @since  __DEPLOY_VERSION__
 */
class ListEntriesCommand extends AbstractKeychainCommand
{
	/**
	 * Execute the command.
	 *
	 * @return  integer  The exit code for the command.
	 */
	public function execute(): int
	{
		$symfonyStyle = $this->createSymfonyStyle();
		$symfonyStyle->title('List Keychain Entries');

		$this->initialiseKeychain();

		$printValues = $this->getApplication()->getConsoleInput()->getOption('print-values');

		$rows = [];

		foreach ($this->keychain->toArray() as $key => $value)
		{
			$row = [$key];

			if ($printValues)
			{
				$row[] = $value;
			}

			$rows[] = $row;
		}

		$headers = ['Key'];

		if ($printValues)
		{
			$headers[] = 'Value';
		}

		$symfonyStyle->table($headers, $rows);

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

		$this->setName('keychain:list');
		$this->setDescription('Lists all entries in the keychain');

		$this->addOption(
			'print-values',
			null,
			InputOption::VALUE_NONE,
			"Flag indicating the keychain's values should be printed"
		);
	}
}

<?php
/**
 * Part of the Joomla Framework Keychain Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License Version 2.1 or Later
 */

namespace Joomla\Keychain\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command class to list entries in a keychain
 *
 * @since  2.0.0-beta
 */
class ListEntriesCommand extends AbstractKeychainCommand
{
	/**
	 * The default command name
	 *
	 * @var    string|null
	 * @since  2.0.0-beta
	 */
	protected static $defaultName = 'keychain:list';

	/**
	 * Internal function to execute the command.
	 *
	 * @param   InputInterface   $input   The input to inject into the command.
	 * @param   OutputInterface  $output  The output to inject into the command.
	 *
	 * @return  integer  The command exit code
	 *
	 * @since   2.0.0-beta
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$symfonyStyle = new SymfonyStyle($input, $output);
		$symfonyStyle->title('List Keychain Entries');

		$printValues = $input->getOption('print-values');

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
	 * Configure the command.
	 *
	 * @return  void
	 *
	 * @since   2.0.0-beta
	 */
	protected function configure(): void
	{
		parent::configure();

		$this->setDescription('Lists all entries in the keychain');

		$this->addOption(
			'print-values',
			null,
			InputOption::VALUE_NONE,
			"Flag indicating the keychain's values should be printed"
		);
	}
}

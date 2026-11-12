<?php

/**
 * Part of the Joomla Framework Registry Package
 *
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command class to add an entry to a keychain
 *
 * @since  4.1
 */
class AddEntryCommand extends AbstractKeychainCommand
{
    /**
     * The default command name
     *
     * @var    string|null
     * @since  2.0.0
     */
    protected static $defaultName = 'keychain:add-entry';

    /**
     * Internal function to execute the command.
     *
     * @param   InputInterface   $input   The input to inject into the command.
     * @param   OutputInterface  $output  The output to inject into the command.
     *
     * @return  integer  The command exit code
     *
     * @since   2.0.0
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->title('Add Keychain Entry');

        $entryName  = $input->getArgument('entry-name');
        $entryValue = $input->getArgument('entry-value');

        if ($this->keychain->exists($entryName)) {
            $symfonyStyle->warning(
                sprintf(
                    'An entry already exists with the key `%s`, use the `keychain:edit-entry` command to edit it.',
                    $entryName
                )
            );

            return 1;
        }

        $this->keychain->set($entryName, $entryValue);

        if (!$this->saveKeychain()) {
            $symfonyStyle->error('The entry was not added to the keychain.');

            return 1;
        }

        $symfonyStyle->success('The entry was added to the keychain.');

        return 0;
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
        parent::configure();

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

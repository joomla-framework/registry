## Command Line Management

The Keychain package provides a suite of commands to enable managing your keychains from a command line interface.

### Prerequisites

In order to use these commands, your application must use the `joomla/console` package.

### Setup

The command classes each have one required dependency, a configured `Joomla\Crypt\Crypt` instance which will be used to read the encrypted file,
and if necessary encrypt the file when saving updates.  Once the `Crypt` instance is configured, commands can be added to the console application
through its `addCommand` method.  A basic example is below.

```php
<?php

use Joomla\Console\Application;
use Joomla\Crypt\Crypt;
use Joomla\Keychain\Command\AddEntryCommand;
use Joomla\Keychain\Command\DeleteEntryCommand;
use Joomla\Keychain\Command\EditEntryCommand;
use Joomla\Keychain\Command\ListEntriesCommand;
use Joomla\Keychain\Command\ReadEntryCommand;

$crypt = new Crypt;

$app = new Application;
$app->addCommand(new AddEntryCommand($crypt));
$app->addCommand(new DeleteEntryCommand($crypt));
$app->addCommand(new EditEntryCommand($crypt));
$app->addCommand(new ListEntriesCommand($crypt));
$app->addCommand(new ReadEntryCommand($crypt));
$app->execute();

```

### Running Commands

With the above configuration, the following commands are available:

```sh
Available commands:
 keychain
  keychain:add-entry     Adds an entry to the keychain
  keychain:delete-entry  Deletes an entry in the keychain
  keychain:edit-entry    Edits an entry in the keychain
  keychain:list          Lists all entries in the keychain
  keychain:read-entry    Reads a single entry in the keychain
```

All of the provided commands have at least one required argument, a file path to the location the keychain file is stored on your filesystem.
Other commands have additional required arguments and available options, by default these can be seen by passing the `--help` option to each command,
i.e. `keychain:edit-entry --help`.

## `joomla/console` Integration

The Event package can be integrated with an application using the `joomla/console` package to provide additional helpers in your application.

### Commands

#### List Information About the Event Dispatcher

The `debug:event-dispatcher` command can be used to list information about your application's event dispatcher, including the events which are subscribed to and the listeners for those events. Instantiating this command requires the dispatcher to be provided.

```php
<?php

// /path/to/console.php
use Joomla\Console\Application;
use Joomla\Event\Command\DebugEventDispatcherCommand;
use Joomla\Event\Dispatcher;

$application = new Application;

$dispatcher = new Dispatcher;

$command = new DebugEventDispatcherCommand($dispatcher);

$application->addCommand($command);

$application->execute();
```

```bash
php /path/to/console.php debug:event-dispatcher
```

The command can be filtered to list information about a single event by providing the event name as an argument.

```bash
# This will filter for the Joomla\Application\ApplicationEvents::BEFORE_EXECUTE event
php /path/to/console.php debug:event-dispatcher application.before_execute
```

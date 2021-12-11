## `joomla/console` Integration

The Router package can be integrated with an application using the `joomla/console` package to provide additional helpers in your application.

### Commands

#### List Information About the Router

The `debug:router` command can be used to list information about your application's router, including the configured routes and the controllers configured to handle those routes.

```php
<?php

// /path/to/console.php
use Joomla\Console\Application;
use Joomla\Router\Router;
use Joomla\Router\Command\DebugRouterCommand;

$application = new Application;

$router = new Router;

$command = new DebugRouterCommand($router);

$application->addCommand($command);

$application->execute();
```

```bash
php /path/to/console.php debug:router
```

By default, the command does not list information about a route's controller. You can pass the `--show-controllers` option to show this extra information if desired.

```bash
php /path/to/console.php debug:router --show-controllers
```

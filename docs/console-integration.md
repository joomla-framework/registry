## `joomla/console` Integration

The Session package can be integrated with an application using the `joomla/console` package to provide additional helpers in your application.

### Commands

#### Create Database Table

The `session:create-table` command can be used when your application uses the `Joomla\Session\Handler\DatabaseHandler` as its session handler to ensure the required database table has been created. Instantiating this command requires the database driver to be provided.

```php
// /path/to/console.php
use Joomla\Console\Application;
use Joomla\Database\DatabaseFactory;
use Joomla\Session\Command\CreateSessionTableCommand;

$application = new Application;

$dbFactory = new DatabaseFactory;
$dbDriver = $dbFactory->getDriver('mysql', []);

$command = new CreateSessionTableCommand($dbDriver);

$application->addCommand($command);

$application->execute();
```

```bash
php /path/to/console.php session:create-table
```

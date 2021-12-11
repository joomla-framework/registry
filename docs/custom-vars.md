## How to Pass Extra Information from a Route to a Controller

The Joomla Router allows parameters inside the defaults collection that don't have to match a placeholder in the route
path. This means, you can use the defaults array to specify extra parameters that will then be accessible for use in
your Request object or controller.

```php
use Joomla\Router\Router;

$router = new Router;
$router->addRoute(
    'GET',
    '/user/:id',
    'UserController@show',
    array(
        'id' => '(\d+)'
    ),
    array(
        'id': 0,
        'username': 'Fred'
    )
);
```


As you can see, the username variable was never defined inside the route path, but you can still access its value from
the return once the URL is parsed.

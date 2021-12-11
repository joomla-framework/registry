## Updating from v1 to v2

### Minimum supported PHP version raised

All Framework packages now require PHP 7.2 or newer.

### PSR-11 Support

Version 2 of the DI package implements the [PSR-11](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md) `ContainerInterface`.

### `Container::extend()` now allows any callable

The `Joomla\DI\Container::extend()` method previously typehinted, and inherently required, a `Closure` instance for its
decorating callback. The typehint on this method has been changed to `callable` and now allows any callable resource
to serve as the decorator.

### `Container::exists()` deprecated

The `Joomla\DI\Container::exists()` method has been deprecated in favor of the PSR-11 defined `has()` method.

### `Container::get()` signature changed

The second (optional) argument on `Joomla\DI\Container::get()` to force recreation on shared resources was removed.
Use `Joomla\DI\Container::getNewInstance()` instead.

### `ContainerAwareInterface::getContainer()` removed

The `Joomla\DI\ContainerAwareInterface::getContainer()` method has been removed from the interface.  Container aware
objects are no longer required to implement this method.

### `ContainerAwareTrait::getContainer()` visibility changed

The visibility of `Joomla\DI\ContainerAwareTrait::getContainer()` has changed from public to protected.  Container aware
objects may continue using this trait to satisfy the `ContainerAwareInterface` requirements and continue to internally
use the `getContainer()` method as a means for accessing the container with checks ensuring the container has been set
to the class.

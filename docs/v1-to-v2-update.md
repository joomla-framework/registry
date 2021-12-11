## Updating from v1 to v2

The following changes were made to the View package between v1 and v2.

### Minimum supported PHP version raised

All Framework packages now require PHP 7.2 or newer.

### `ViewInterface::escape()` removed

The `Joomla\View\ViewInterface::escape()` method has been removed, escaping should still be handled within views as necessary but there is no longer a public facing method to escape a string.

### Models no longer compulsory

`Joomla\View\AbstractView` instances no longer mandate a model class, applications are welcome to require models in their subclasses or use the new data store API to set view data.

### `AbstractHtmlView` removed

The `Joomla\View\AbstractHtmlView` class has been removed. A new concrete `Joomla\View\HtmlView` is available to generate HTML views using any templating engine by implementing `Joomla\Renderer\RendererInterface` (with a number of templating engines already supported in the `joomla/renderer` package).

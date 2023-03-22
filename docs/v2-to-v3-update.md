## Updating from Version 2 to Version 3

The following changes were made to the Registry package between version 2 and version 3.

### Minimum supported PHP version raised

All Framework packages now require PHP 8.1 or newer.

### Enforce input types

* The `Joomla\Registry\Format\Xml::getValueFromNode()` method now requires the `$node` parameter to be a `SimpleXMLElement` object.

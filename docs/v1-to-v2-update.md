## Updating from v1 to v2

The following changes were made to the Registry package between v1 and v2.

### Minimum supported PHP version raised

All Framework packages now require PHP 7.2 or newer.

### Object caching removed

The `Joomla\Registry\Registry::getInstance()` method has been removed completely and `Joomla\Registry\Factory::getFormat()` will now
return a new `Joomla\Registry\FormatInterface` instance on each call.

### Joomla\Registry\AbstractRegistryFormat removed

In v1, the `AbstractRegistryFormat` class was deprecated in favor of the `FormatInterface`. In v2, instead of extending the old
abstract class, format objects must now implement the interface.

### Joomla\Registry\Format\Php is now type aware

In v1, the `Php` format object would create a registry class that implicitly converted all data to string notation. In v2, the
registry class respects the data type of each value.

### Joomla\Registry\FormatInterface::objectToString() $options argument typehinted

The `FormatInterface::objectToString()` method (previously `AbstractRegistryFormat::objectToString()`) now typehints the `$options`
argument as an array; this was not enforced in v1.

### Joomla\Registry\Registry methods typehinted

Several methods in the `Registry` class are now typehinted, this affects methods with an argument requiring an array and the
`Registry::merge()` method which now typehints the `$source` argument.

### Joomla\Registry\Registry::extract() always returns a Registry

Previously when there was no data for a key, `Registry::extract()` would return a null value. In 2.0, the null return is removed and an empty `Registry` is returned.

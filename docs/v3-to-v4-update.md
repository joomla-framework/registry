## Updating from Version 3 to Version 4

The following changes were made to the Registry package between version 3 and version 4.

### Minimum supported PHP version raised

All Framework packages now require PHP 8.3 or newer.

### Enforce strings as input for `stringToObject()`

The input to `FormatInterface::stringToObject()` has always been string in the docblock, but starting now it is also enforced by a typehint in the method header. Make sure to really hand in strings.

### Separator has been removed from `Registry::set()` and `Registry::loadArray()`

`Registry::set()` allowed to hand in an on-the-fly separator, which could lead to conflicts with the set separator in the object. In version 4 this has been removed and since `Registry::loadArray()` also uses `Registry::set()` internally, its been removed from this method as well.

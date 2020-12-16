## Updating from v1 to v2

### Minimum supported PHP version raised

All Framework packages now require PHP 7.2 or newer.

### Exception Class Constructors
`Joomla\Archive\Exception\UnsupportedArchiveException` now requires an additional constructor argument. `$adapterType`
which contains the adapter that can't be matched to a valid parser. This comes with a matching argument `getUnsupportedAdapterType`
in the exception, which can be used to create localised error messages to display to users.

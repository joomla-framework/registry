# The Filter Package [![Build Status](https://ci.joomla.org/api/badges/joomla-framework/filter/status.svg?ref=refs/heads/2.0-dev)](https://ci.joomla.org/joomla-framework/filter)

[![Latest Stable Version](https://poser.pugx.org/joomla/filter/v/stable)](https://packagist.org/packages/joomla/filter)
[![Total Downloads](https://poser.pugx.org/joomla/filter/downloads)](https://packagist.org/packages/joomla/filter)
[![Latest Unstable Version](https://poser.pugx.org/joomla/filter/v/unstable)](https://packagist.org/packages/joomla/filter)
[![License](https://poser.pugx.org/joomla/filter/license)](https://packagist.org/packages/joomla/filter)

## Installation via Composer

Add `"joomla/filter": "~2.0.*@dev"` to the require block in your composer.json and then run `composer install`.

```json
{
	"require": {
		"joomla/filter": "~2.0"
	}
}
```

Alternatively, you can simply run the following from the command line:

```sh
composer require joomla/filter "~2.0"
```

If you want to include the test sources, use

```sh
composer require --prefer-source joomla/filter "~2.0"
```

Note that the `Joomla\Language` package is an optional dependency and is only required if the application requires the use of `OutputFilter::stringURLSafe`.

## Upgrades from 1 to 2
Note the InputFilter static class constants have been renamed:

| Before  | After |
| ------------- | ------------- |
| InputFilter::TAGS_WHITELIST  | InputFilter::ONLY_ALLOW_DEFINED_TAGS  |
| InputFilter::TAGS_BLACKLIST  | InputFilter::ONLY_BLOCK_DEFINED_TAGS  |
| InputFilter::ATTR_WHITELIST  | InputFilter::ONLY_ALLOW_DEFINED_ATTRIBUTES  |
| InputFilter::ATTR_BLACKLIST  | InputFilter::ONLY_BLOCK_DEFINED_ATTRIBUTES  |

The public property `InputFilter::tagBlacklist` has been renamed to `InputFilter::blockedTags`. Similarly
`InputFilter::attrBlacklist` has been renamed to `InputFilter::blockedAttributes`

All code usage of these properties remains unchanged.

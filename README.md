# The DI Package [![Build Status](https://ci.joomla.org/api/badges/joomla-framework/di/status.svg?ref=refs/heads/2.0-dev)](https://ci.joomla.org/joomla-framework/di)

[![Latest Stable Version](https://poser.pugx.org/joomla/di/v/stable)](https://packagist.org/packages/joomla/di)
[![Total Downloads](https://poser.pugx.org/joomla/di/downloads)](https://packagist.org/packages/joomla/di)
[![Latest Unstable Version](https://poser.pugx.org/joomla/di/v/unstable)](https://packagist.org/packages/joomla/di)
[![License](https://poser.pugx.org/joomla/di/license)](https://packagist.org/packages/joomla/di)

The Joomla! **Dependency Injection** package provides a powerful [PSR-11](http://www.php-fig.org/psr/psr-11/) compatible Inversion of Control (IoC) Container for your application.

## Requirements

* PHP 7.2.5 or later

## Installation via Composer

Add `"joomla/di": "~2.0"` to the require block in your composer.json and then run `composer install`.

```json
{
	"require": {
		"joomla/di": "~2.0"
	}
}
```

Alternatively, you can simply run the following from the command line:

```sh
composer require joomla/di "~2.0"
```

If you want to include the test sources and docs, use

```sh
composer require --prefer-source joomla/di "~2.0"
```

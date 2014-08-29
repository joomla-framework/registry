## The Crypt Package [![Build Status](https://travis-ci.org/joomla-framework/crypt.png?branch=master)](https://travis-ci.org/joomla-framework/crypt)

The Crypt password provides a set of classes that can be used for encrypting and hashing data.

### Interfaces

#### `PasswordInterface`

`PasswordInterface` is an interface that requires a class to be implemented with a create and a verify method.

The create method should take a plain text password and a type and return a hashed password.

The verify method should accept a plain text password and a hashed password and return a boolean indicating whether or not the password matched the password in the hash.

The `PasswordInterface` interface defines the following constants for use with implementations:

- `PasswordInterface::BLOWFISH`
- `PasswordInterface::JOOMLA`
- `PasswordInterface::MD5`
- `PasswordInterface::PBKDF`

### Classes

#### `Password\PasswordSimple`

##### Usage

In addition to the interface `PasswordInterface` there is also a basic implementation provided which provides for use with the most common password schemes. This if found in the `Password\PasswordSimple` class.

Aside from the two methods create and verify methods, this implementation also adds an additional method called setCost. This method is used to set a cost parameter for methods that support workload factors. It takes an integer cost factor as a parameter.

`Password\Simple` provides support for bcrypt, MD5 and the traditional Joomla! CMS hashing scheme. The hash format can be specified during hash creation by using the constants `PasswordInterface::BLOWFISH`, `PasswordInterface::MD5`, `PasswordInterface::JOOMLA`, and `PasswordInterface::PBKDF`. An appropriate salt will be automatically generated when required.

## Changes From 1.x

The package has been refactored from 1.x to 2.0 to be PSR-4 compliant, and in doing so required renaming of several classes.  Below is a table of renamed classes.

| Old Name                           | New Name                                 |
| ---------                          | -----                                    |
| `\Joomla\Crypt\Cipher_3DES`        | `\Joomla\Crypt\Cipher\Cipher3DES`        |
| `\Joomla\Crypt\Cipher_Blowfish`    | `\Joomla\Crypt\Cipher\CipherBlowfish`    |
| `\Joomla\Crypt\Cipher_Mcrypt`      | `\Joomla\Crypt\Cipher\CipherMcrypt`      |
| `\Joomla\Crypt\Cipher_Rijndael256` | `\Joomla\Crypt\Cipher\CipherRijndael256` |
| `\Joomla\Crypt\Cipher_Simple`      | `\Joomla\Crypt\Cipher\CipherSimple`      |
| `\Joomla\Crypt\Password\Simple`    | `\Joomla\Crypt\Password\PasswordSimple`  |

## Installation via Composer

Add `"joomla/crypt": "2.0.*@dev"` to the require block in your composer.json and then run `composer install`.

```json
{
	"require": {
		"joomla/crypt": "2.0.*@dev"
	}
}
```

Alternatively, you can simply run the following from the command line:

```sh
composer require joomla/crypt "2.0.*@dev"
```

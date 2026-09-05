# Joomla Registry Package — Documentation

A hierarchical key/value store with dot-notation paths, backed by five interchangeable format
handlers (JSON, INI, XML, YAML, PHP), plus an optional encrypted variant.

## Guide

* [Overview](overview.md) — reading and writing paths, loading and dumping formats
* [Keychain](keychain.md) — the encrypted store and its console commands, and how to move over
  from `joomla/keychain`

## Upgrading

* [Updating from v1 to v2](v1-to-v2-update.md)
* [Updating from v2 to v3](v2-to-v3-update.md) — PHP 8.1, PSR-12, no API changes
* [Updating from v3 to v4](v3-to-v4-update.md) — PHP 8.3, magic accessors and `$separator` removed

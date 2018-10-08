<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Registry of file parsers
 *
 * @since  __DEPLOY_VERSION__
 */
class ParserRegistry
{
	/**
	 * A map of the registered parsers
	 *
	 * @var    ParserInterface[]
	 * @since  __DEPLOY_VERSION__
	 */
	private $parserMap = [];

	/**
	 * Register a parser, overridding a previously registered parser for the given type
	 *
	 * @param   ParserInterface  $parser  The parser to registery
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function add(ParserInterface $parser)
	{
		$this->parserMap[$parser->getType()] = $parser;
	}

	/**
	 * Get the parser for a given type
	 *
	 * @param   string  $type  The parser type to retrieve
	 *
	 * @return  ParserInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function get(string $type): ParserInterface
	{
		if (!$this->has($type))
		{
			throw new \InvalidArgumentException(sprintf('There is not a parser registered for the `%s` type.', $type));
		}

		return $this->parserMap[$type];
	}

	/**
	 * Check if a parser is registered for the given type
	 *
	 * @param   string  $type  The parser type to check (typically the file extension)
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function has(string $type): bool
	{
		return isset($this->parserMap[$type]);
	}
}

<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Language\Text;

/**
 * Text object service provider
 *
 * @since  __DEPLOY_VERSION__
 */
class TextProvider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	public function register(Container $container)
	{
		$container->share(
			'Joomla\\Language\\Text',
			function () use ($container)
			{
				return $container->buildObject('\\Joomla\\Language\\Text');
			}, true
		);
	}
}

<?php
/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Language\Language;

/**
 * Language object service provider
 *
 * @since  __DEPLOY_VERSION__
 */
class LanguageProvider implements ServiceProviderInterface
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
			'Joomla\\Language\\Language',
			function () use ($container)
			{
				/** @var \Joomla\Registry\Registry $config */
				$config = $container->get('config');

				$baseLangDir = $config->get('language.basedir');
				$defaultLang = $config->get('language.default', 'en-GB');
				$debug       = $config->get('language.debug', false);

				return Language::getInstance($defaultLang, $baseLangDir, $debug);
			}, true
		);
	}
}

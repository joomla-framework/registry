<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\OAuth1\Tests\Stub;

use Joomla\OAuth1\Client;

/**
 * Stub client object for testing
 */
class TestClient extends Client
{
	/**
	 * {@inheritdoc}
	 */
	public function verifyCredentials()
	{
		if (!strcmp($this->token['key'], 'valid'))
		{
			return true;
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validateResponse($url, $response)
	{
		if ($response->code < 200 || $response->code > 399)
		{
			throw new \DomainException($response->body);
		}
	}
}

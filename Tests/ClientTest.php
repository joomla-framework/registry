<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\OAuth1\Tests;

use Joomla\Application\SessionAwareWebApplicationInterface;
use Joomla\Http\Http;
use Joomla\Input\Input;
use Joomla\OAuth1\Tests\Stub\TestClient;
use Joomla\Registry\Registry;
use Joomla\Session\SessionInterface;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\OAuth1\Client.
 *
 * @backupGlobals enabled
 */
class ClientTest extends TestCase
{
	/**
	 * Input object for the Client object.
	 *
	 * @var  Input
	 */
	protected $input;

	/**
	 * Options for the Client object.
	 *
	 * @var  Registry
	 */
	protected $options;

	/**
	 * Mock HTTP object.
	 *
	 * @var  Http|MockObject
	 */
	protected $client;

	/**
	 * An instance of the object to test.
	 *
	 * @var  TestClient
	 */
	protected $object;

	/**
	 * The application object to send HTTP headers for redirects.
	 *
	 * @var   SessionAwareWebApplicationInterface|MockObject
	 */
	protected $application;

	/**
	 * Sample JSON string.
	 *
	 * @var  string
	 */
	protected $sampleString = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

	/**
	 * Sample JSON error message.
	 *
	 * @var  string
	 */
	protected $errorString = '{"errorCode":401, "message": "Generic error"}';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		$_SERVER['HTTP_HOST']       = 'example.com';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
		$_SERVER['REQUEST_URI']     = '/index.php';
		$_SERVER['SCRIPT_NAME']     = '/index.php';

		$key    = 'TEST_KEY';
		$secret = 'TEST_SECRET';
		$my_url = 'TEST_URL';

		$this->options     = new Registry;
		$this->client      = $this->createMock(Http::class);
		$this->input       = new Input([]);
		$this->application = $this->createMock(SessionAwareWebApplicationInterface::class);

		$this->application->expects($this->any())
			->method('getSession')
			->willReturn($this->createMock(SessionInterface::class));

		$this->options->set('consumer_key', $key);
		$this->options->set('consumer_secret', $secret);
		$this->object = new TestClient($this->application, $this->client, $this->input, $this->options);
	}

	/**
	 * Tests the constructor to ensure only arrays or ArrayAccess objects are allowed
	 */
	public function testConstructorDisallowsNonArrayObjects()
	{
		$this->expectException(\InvalidArgumentException::class);

		new TestClient($this->application, $this->client, $this->input, new \stdClass);
	}

	/**
	 * Provides test data.
	 *
	 * @return  \Generator
	 */
	public function seedAuthenticate(): \Generator
	{
		// Token, fail and oauth version.
		yield [['key' => 'valid', 'secret' => 'valid'], false, '1.0'];
		yield [null, false, '1.0'];
		yield [null, false, '1.0a'];
		yield [null, true, '1.0a'];
	}

	/**
	 * Tests the authenticate method
	 *
	 * @param   array    $token    The passed token.
	 * @param   boolean  $fail     Mark if should fail or not.
	 * @param   string   $version  Specify oauth version 1.0 or 1.0a.
	 *
	 * @dataProvider seedAuthenticate
	 */
	public function testAuthenticate($token, $fail, $version)
	{
		// Already got some credentials stored?
		if (!\is_null($token))
		{
			$this->object->setToken($token);
			$result = $this->object->authenticate();
			$this->assertEquals($result, $token);
		}
		else
		{
			$this->object->setOption('requestTokenURL', 'https://example.com/request_token');
			$this->object->setOption('authoriseURL', 'https://example.com/authorize');
			$this->object->setOption('accessTokenURL', 'https://example.com/access_token');

			// Request token.
			$returnData = new \stdClass;
			$returnData->code = 200;
			$returnData->body = 'oauth_token=token&oauth_token_secret=secret&oauth_callback_confirmed=true';

			$this->client->expects($this->at(0))
				->method('post')
				->with($this->object->getOption('requestTokenURL'))
				->willReturn($returnData);

			$input = TestHelper::getValue($this->object, 'input');
			$input->set('oauth_verifier', null);

			if (strcmp($version, '1.0a') === 0)
			{
				$this->object->setOption('callback', 'TEST_URL');
			}

			$this->object->authenticate();

			$token = $this->object->getToken();
			$this->assertEquals($token['key'], 'token');
			$this->assertEquals($token['secret'], 'secret');

			// Access token.
			$input = TestHelper::getValue($this->object, 'input');

			TestHelper::setValue($this->object, 'version', $version);

			if ($version === '1.0a')
			{
				$data = ['oauth_verifier' => 'verifier', 'oauth_token' => 'token'];
			}
			else
			{
				$data = ['oauth_token' => 'token'];
			}

			TestHelper::setValue($input, 'data', $data);

			// Get mock session
			/** @var SessionInterface|MockObject $mockSession */
			$mockSession = $this->application->getSession();

			if ($fail)
			{
				$mockSession->expects($this->at(0))
					->method('get')
					->with('oauth_token.key')
					->willReturn('bad');

				$mockSession->expects($this->at(1))
					->method('get')
					->with('oauth_token.secret')
					->willReturn('session');

				$this->expectException(\DomainException::class);

				$this->object->authenticate();
			}

			$mockSession->expects($this->at(0))
				->method('get')
				->with('oauth_token.key')
				->willReturn('token');

			$mockSession->expects($this->at(1))
				->method('get')
				->with('oauth_token.secret')
				->willReturn('secret');

			$returnData = new \stdClass;
			$returnData->code = 200;
			$returnData->body = 'oauth_token=token_key&oauth_token_secret=token_secret';

			$this->client->expects($this->at(0))
				->method('post')
				->with($this->object->getOption('accessTokenURL'))
				->willReturn($returnData);

			$result = $this->object->authenticate();

			$this->assertEquals($result['key'], 'token_key');
			$this->assertEquals($result['secret'], 'token_secret');
		}
	}

	/**
	 * Tests the generateRequestToken method - failure
	 */
	public function testGenerateRequestTokenFailure()
	{
		$this->expectException(\DomainException::class);

		$this->object->setOption('requestTokenURL', 'https://example.com/request_token');

		$returnData = new \stdClass;
		$returnData->code = 200;
		$returnData->body = 'oauth_token=token&oauth_token_secret=secret&oauth_callback_confirmed=false';

		$this->client->expects($this->at(0))
			->method('post')
			->with($this->object->getOption('requestTokenURL'))
			->willReturn($returnData);

		TestHelper::invoke($this->object, 'generateRequestToken');
	}

	/**
	 * Provides test data.
	 *
	 * @return  \Generator
	 */
	public function seedOauthRequest(): \Generator
	{
		yield 'GET request' => ['GET'];
		yield 'PUT request' => ['PUT'];
		yield 'DELETE request' => ['DELETE'];
	}

	/**
	 * Tests the oauthRequest method
	 *
	 * @param   string  $method  The request method.
	 *
	 * @dataProvider seedOauthRequest
	 */
	public function testOauthRequest($method)
	{
		$returnData = new \stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleString;

		if ($method === 'PUT')
		{
			$data = ['key1' => 'value1', 'key2' => 'value2'];
			$this->client->expects($this->at(0))
				->method($method)
				->with('www.example.com', $data)
				->willReturn($returnData);

			$this->assertSame(
				$returnData,
				$this->object->oauthRequest(
					'www.example.com',
					$method,
					['oauth_token' => '1235'],
					$data,
					['Content-Type' => 'multipart/form-data']
				)
			);
		}
		else
		{
			$this->client->expects($this->at(0))
				->method($method)
				->with('www.example.com')
				->willReturn($returnData);

			$this->assertSame(
				$returnData,
				$this->object->oauthRequest(
					'www.example.com',
					$method,
					['oauth_token' => '1235'],
					[],
					['Content-Type' => 'multipart/form-data']
				)
			);
		}
	}

	/**
	 * Tests the safeEncode
	 *
	 * @return  void
	 */
	public function testSafeEncodeEmpty()
	{
		$this->assertEmpty(
			$this->object->safeEncode(null)
		);
	}
}

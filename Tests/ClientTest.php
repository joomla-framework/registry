<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\OAuth2\Tests;

use Joomla\Application\WebApplicationInterface;
use Joomla\Http\Http;
use Joomla\Input\Input;
use Joomla\OAuth2\Client;
use Joomla\Registry\Registry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\OAuth2\Client.
 *
 * @backupGlobals enabled
 */
class ClientTest extends TestCase
{
	/**
	 * Options for the Client object.
	 *
	 * @var  Registry
	 */
	protected $options;

	/**
	 * Mock client object.
	 *
	 * @var  Http|MockObject
	 */
	protected $client;

	/**
	 * The input object to use in retrieving GET/POST data.
	 *
	 * @var  Input
	 */
	protected $input;

	/**
	 * The application object to send HTTP headers for redirects.
	 *
	 * @var  WebApplicationInterface|MockObject
	 */
	protected $application;

	/**
	 * Object under test.
	 *
	 * @var  Client
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$_SERVER['HTTP_HOST'] = 'mydomain.com';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
		$_SERVER['REQUEST_URI'] = '/index.php';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$this->options = new Registry;
		$this->http    = $this->createMock(Http::class);
		$this->input   = new Input([]);

		$this->application = $this->createMock(WebApplicationInterface::class);

		$this->object = new Client($this->options, $this->http, $this->input, $this->application);
	}

	/**
	 * Tests the auth method
	 */
	public function testAuth()
	{
		$this->object->setOption('authurl', 'https://accounts.google.com/o/oauth2/auth');
		$this->object->setOption('clientid', '01234567891011.apps.googleusercontent.com');
		$this->object->setOption('scope', ['https://www.googleapis.com/auth/adsense', 'https://www.googleapis.com/auth/calendar']);
		$this->object->setOption('redirecturi', 'http://localhost/oauth');
		$this->object->setOption('requestparams', ['access_type' => 'offline', 'approval_prompt' => 'auto']);
		$this->object->setOption('sendheaders', true);

		$this->application->expects($this->any())
			->method('redirect')
			->willReturn(true);

		$this->object->authenticate();

		$this->object->setOption('tokenurl', 'https://accounts.google.com/o/oauth2/token');
		$this->object->setOption('clientsecret', 'jeDs8rKw_jDJW8MMf-ff8ejs');
		$this->input->set('code', '4/wEr_dK8SDkjfpwmc98KejfiwJP-f4wm.kdowmnr82jvmeisjw94mKFIJE48mcEM');

		$this->http->expects($this->once())
			->method('post')
			->willReturnCallback([$this, 'encodedGrantOauthCallback']);

		$result = $this->object->authenticate();

		$this->assertEquals('accessvalue', $result['access_token']);
		$this->assertEquals('refreshvalue', $result['refresh_token']);
		$this->assertEquals(3600, $result['expires_in']);
		$this->assertLessThanOrEqual(1, time() - $result['created']);
	}

	/**
	 * Tests the auth method with JSON data
	 */
	public function testAuthJson()
	{
		$this->object->setOption('tokenurl', 'https://accounts.google.com/o/oauth2/token');
		$this->object->setOption('clientsecret', 'jeDs8rKw_jDJW8MMf-ff8ejs');
		$this->input->set('code', '4/wEr_dK8SDkjfpwmc98KejfiwJP-f4wm.kdowmnr82jvmeisjw94mKFIJE48mcEM');

		$this->http->expects($this->once())
			->method('post')
			->willReturnCallback([$this, 'encodedGrantOauthCallback']);

		$result = $this->object->authenticate();

		$this->assertEquals('accessvalue', $result['access_token']);
		$this->assertEquals('refreshvalue', $result['refresh_token']);
		$this->assertEquals(3600, $result['expires_in']);
		$this->assertLessThanOrEqual(1, time() - $result['created']);
	}

	/**
	 * Tests the isAuth method
	 */
	public function testIsAuth()
	{
		$this->assertFalse($this->object->isAuthenticated());

		$token['access_token'] = 'accessvalue';
		$token['refresh_token'] = 'refreshvalue';
		$token['created'] = time();
		$token['expires_in'] = 3600;
		$this->object->setToken($token);

		$this->assertTrue($this->object->isAuthenticated());

		$token['created'] = time() - 4000;
		$token['expires_in'] = 3600;
		$this->object->setToken($token);

		$this->assertFalse($this->object->isAuthenticated());
	}

	/**
	 * Tests the createUrl method
	 */
	public function testCreateUrl()
	{
		$this->object->setOption('authurl', 'https://accounts.google.com/o/oauth2/auth');
		$this->object->setOption('clientid', '01234567891011.apps.googleusercontent.com');
		$this->object->setOption('scope', ['https://www.googleapis.com/auth/adsense', 'https://www.googleapis.com/auth/calendar']);
		$this->object->setOption('state', '123456');
		$this->object->setOption('redirecturi', 'http://localhost/oauth');
		$this->object->setOption('requestparams', ['access_type' => 'offline', 'approval_prompt' => 'auto']);

		$url      = $this->object->createUrl();
		$expected = 'https://accounts.google.com/o/oauth2/auth?response_type=code';
		$expected .= '&client_id=01234567891011.apps.googleusercontent.com';
		$expected .= '&redirect_uri=http%3A%2F%2Flocalhost%2Foauth';
		$expected .= '&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fadsense';
		$expected .= '+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fcalendar';
		$expected .= '&state=123456&access_type=offline&approval_prompt=auto';
		$this->assertEquals($expected, $url);
	}

	/**
	 * Tests the query method
	 */
	public function testQuery()
	{
		$token['access_token']  = 'accessvalue';
		$token['refresh_token'] = 'refreshvalue';
		$token['created']       = time() - 1800;
		$token['expires_in']    = 600;
		$this->object->setToken($token);

		$result = $this->object->query('https://www.googleapis.com/auth/calendar', ['param' => 'value'], [], 'get');
		$this->assertFalse($result);

		$token['expires_in'] = 3600;
		$this->object->setToken($token);

		$this->http->expects($this->once())
			->method('post')
			->willReturnCallback([$this, 'queryOauthCallback']);

		$result = $this->object->query('https://www.googleapis.com/auth/calendar', ['param' => 'value'], [], 'post');

		$this->assertEquals($result->body, 'Lorem ipsum dolor sit amet.');
		$this->assertEquals(200, $result->code);

		$this->object->setOption('authmethod', 'get');
		$this->http->expects($this->once())
			->method('get')
			->willReturnCallback([$this, 'getOauthCallback']);

		$result = $this->object->query('https://www.googleapis.com/auth/calendar', ['param' => 'value'], [], 'get');

		$this->assertEquals($result->body, 'Lorem ipsum dolor sit amet.');
		$this->assertEquals(200, $result->code);
	}

	/**
	 * Tests the setOption method
	 */
	public function testSetOption()
	{
		$this->object->setOption('key', 'value');

		$this->assertEquals(
			'value',
			$this->options->get('key')
		);
	}

	/**
	 * Tests the getOption method
	 */
	public function testGetOption()
	{
		$this->options->set('key', 'value');

		$this->assertEquals(
			'value',
			$this->object->getOption('key')
		);

		$this->assertEquals(
			'bar',
			$this->object->getOption('foo', 'bar')
		);
	}

	/**
	 * Tests the setToken method
	 */
	public function testSetToken()
	{
		$this->object->setToken(['access_token' => 'RANDOM STRING OF DATA']);

		$this->assertSame(
			['access_token' => 'RANDOM STRING OF DATA'],
			$this->options->get('accesstoken')
		);

		$this->object->setToken(['access_token' => 'RANDOM STRING OF DATA', 'expires_in' => 3600]);

		$this->assertSame(
			['access_token' => 'RANDOM STRING OF DATA', 'expires_in' => 3600],
			$this->options->get('accesstoken')
		);

		$this->object->setToken(['access_token' => 'RANDOM STRING OF DATA', 'expires' => 3600]);

		$this->assertSame(
			['access_token' => 'RANDOM STRING OF DATA', 'expires_in' => 3600],
			$this->options->get('accesstoken')
		);
	}

	/**
	 * Tests the getToken method
	 */
	public function testGetToken()
	{
		$this->options->set('accesstoken', ['access_token' => 'RANDOM STRING OF DATA']);

		$this->assertSame(
			['access_token' => 'RANDOM STRING OF DATA'],
			$this->object->getToken()
		);
	}

	/**
	 * Tests the refreshToken method
	 */
	public function testRefreshToken()
	{
		$this->object->setOption('tokenurl', 'https://accounts.google.com/o/oauth2/token');
		$this->object->setOption('clientid', '01234567891011.apps.googleusercontent.com');
		$this->object->setOption('clientsecret', 'jeDs8rKw_jDJW8MMf-ff8ejs');
		$this->object->setOption('redirecturi', 'http://localhost/oauth');
		$this->object->setOption('userefresh', true);
		$this->object->setToken(['access_token' => 'RANDOM STRING OF DATA', 'expires' => 3600, 'refresh_token' => ' RANDOM STRING OF DATA']);

		$this->http->expects($this->once())
			->method('post')
			->willReturnCallback([$this, 'encodedGrantOauthCallback']);

		$result = $this->object->refreshToken();

		$this->assertEquals('accessvalue', $result['access_token']);
		$this->assertEquals('refreshvalue', $result['refresh_token']);
		$this->assertEquals(3600, $result['expires_in']);
		$this->assertLessThanOrEqual(1, time() - $result['created']);
	}

	/**
	 * Tests the refreshToken method with JSON
	 */
	public function testRefreshTokenJson()
	{
		$this->object->setOption('tokenurl', 'https://accounts.google.com/o/oauth2/token');
		$this->object->setOption('clientid', '01234567891011.apps.googleusercontent.com');
		$this->object->setOption('clientsecret', 'jeDs8rKw_jDJW8MMf-ff8ejs');
		$this->object->setOption('redirecturi', 'http://localhost/oauth');
		$this->object->setOption('userefresh', true);
		$this->object->setToken(['access_token' => 'RANDOM STRING OF DATA', 'expires' => 3600, 'refresh_token' => ' RANDOM STRING OF DATA']);

		$this->http->expects($this->once())
			->method('post')
			->willReturnCallback([$this, 'jsonGrantOauthCallback']);

		$result = $this->object->refreshToken();

		$this->assertEquals('accessvalue', $result['access_token']);
		$this->assertEquals('refreshvalue', $result['refresh_token']);
		$this->assertEquals(3600, $result['expires_in']);
		$this->assertLessThanOrEqual(1, time() - $result['created']);
	}

	/**
	 * Callback to mock an encoded & granted OAuth response
	 *
	 * @param   string   $url      Path to the resource.
	 * @param   mixed    $data     Either an associative array or a string to be sent with the request.
	 * @param   array    $headers  An array of name-value pairs to include in the header of the request
	 * @param   integer  $timeout  Read timeout in seconds.
	 *
	 * @return  object
	 */
	public function encodedGrantOauthCallback($url, $data, array $headers = null, $timeout = null)
	{
		$response = new \stdClass;

		$response->code    = 200;
		$response->headers = ['Content-Type' => 'x-www-form-urlencoded'];
		$response->body    = 'access_token=accessvalue&refresh_token=refreshvalue&expires_in=3600';

		return $response;
	}

	/**
	 * Callback to mock a JSON based & granted OAuth response
	 *
	 * @param   string   $url      Path to the resource.
	 * @param   mixed    $data     Either an associative array or a string to be sent with the request.
	 * @param   array    $headers  An array of name-value pairs to include in the header of the request
	 * @param   integer  $timeout  Read timeout in seconds.
	 *
	 * @return  object
	 */
	public function jsonGrantOauthCallback($url, $data, array $headers = null, $timeout = null)
	{
		$response = new \stdClass;

		$response->code    = 200;
		$response->headers = ['Content-Type' => 'application/json'];
		$response->body    = '{"access_token":"accessvalue","refresh_token":"refreshvalue","expires_in":3600}';

		return $response;
	}

	/**
	 * Callback to mock a query based OAuth response
	 *
	 * @param   string   $url      Path to the resource.
	 * @param   mixed    $data     Either an associative array or a string to be sent with the request.
	 * @param   array    $headers  An array of name-value pairs to include in the header of the request
	 * @param   integer  $timeout  Read timeout in seconds.
	 *
	 * @return  object
	 */
	public function queryOauthCallback($url, $data, array $headers = null, $timeout = null)
	{
		$response = new \stdClass;

		$response->code    = 200;
		$response->headers = ['Content-Type' => 'text/html'];
		$response->body    = 'Lorem ipsum dolor sit amet.';

		return $response;
	}

	/**
	 * Callback to mock a OAuth response
	 *
	 * @param   string   $url      Path to the resource.
	 * @param   array    $headers  An array of name-value pairs to include in the header of the request.
	 * @param   integer  $timeout  Read timeout in seconds.
	 *
	 * @return  object
	 */
	public function getOauthCallback($url, array $headers = null, $timeout = null)
	{
		$response = new \stdClass;

		$response->code    = 200;
		$response->headers = ['Content-Type' => 'text/html'];
		$response->body    = 'Lorem ipsum dolor sit amet.';

		return $response;
	}
}

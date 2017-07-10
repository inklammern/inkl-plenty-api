<?php

namespace Inkl\PlentyApi\Client;

use Httpful\Mime;
use Swap\Exception\Exception;

class RestClient
{

	private $endpoint;
	private $accessToken;
	private $refreshToken;
	private $username;
	private $password;

	/**
	 * @param string $endpoint
	 * @param string $accessToken
	 * @param string $refreshToken
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($endpoint, $accessToken, $refreshToken, $username = '', $password = '')
	{
		$this->endpoint = $endpoint;
		$this->accessToken = $accessToken;
		$this->refreshToken = $refreshToken;
		$this->username = $username;
		$this->password = $password;
	}

	public function post($method, $params = [])
	{
		$response = \Httpful\Request::post(sprintf('%s%s', $this->endpoint, $method), http_build_query($params), Mime::FORM)
			->addHeader('Authorization', 'Bearer ' . $this->accessToken)
			->expectsJson()
			->send();

		return $response;
	}

	public function put($method, $params = [])
	{
		$response = \Httpful\Request::put(sprintf('%s%s', $this->endpoint, $method), http_build_query($params), Mime::FORM)
			->addHeader('Authorization', 'Bearer ' . $this->accessToken)
			->expectsJson()
			->send();

		return $response;
	}

	public function get($method, $params = [])
	{
		$response = \Httpful\Request::get(sprintf('%s%s?%s', $this->endpoint, $method, http_build_query($params)))
			->addHeader('Authorization', 'Bearer ' . $this->accessToken)
			->expectsJson()
			->send();

		return $response;
	}

	public function auth()
	{
		if ($this->refreshToken)
		{
			$this->accessToken = '';
			$this->refreshToken = '';

			$response = $this->post('login/refresh', ['refreshToken' => $this->refreshToken]);
			if (isset($response->body->access_token))
			{
				$this->accessToken = $response->body->access_token;
				$this->refreshToken = $response->body->refresh_token;

				return true;
			}
		}

		if (!$this->accessToken)
		{
			$response = $this->post('login', ['username' => $this->username, 'password' => $this->password]);
			if (isset($response->body->access_token))
			{
				$this->accessToken = $response->body->access_token;
				$this->refreshToken = $response->body->refresh_token;

				return true;
			}
		}

		throw new Exception('unable to authenticata');
	}

	/**
	 * @return string
	 */
	public function getAccessToken()
	{
		return $this->accessToken;
	}

	/**
	 * @return string
	 */
	public function getRefreshToken()
	{
		return $this->refreshToken;
	}

}

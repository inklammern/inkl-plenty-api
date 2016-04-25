<?php

namespace Inkl\PlentyApi\Client;

use Zend\Soap\Client;

class SoapClient implements ClientInterface
{
	/** @var Client */
	private $zendSoapClient;
	private $wsdl;
	private $username;
	private $password;
	private $userId;
	private $userToken;
	private $maxCallTries = 5;

	public function __construct($wsdl, $username, $password)
	{
		$this->wsdl = $wsdl;
		$this->username = $username;
		$this->password = $password;
	}

	public function connect($userId = '', $userToken = '')
	{
		$this->zendSoapClient = new Client($this->wsdl);

		$this->userId = $userId;
		$this->userToken = $userToken;

		$this->setSoapHeader();
		$this->checkToken();
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getUserToken()
	{
		return $this->userToken;
	}

	private function checkToken()
	{
		try {
			$this->call('GetServerTime');
		} catch (\Exception $e) {

			if (preg_match('/(token required|invalid token)/is', $e->getMessage())) {
				$this->auth();
			}
		}
	}

	private function auth()
	{
		$result = $this->zendSoapClient->call('GetAuthentificationToken', [
			[
				'Username' => $this->username,
				'Userpass' => $this->password
			]
		]);
		if (!isset($result->Token) || $result->Token == '') throw new \Exception('token could not be fetched (maybe call limit)');
		$this->userId = (string)$result->UserID;
		$this->userToken = (string)$result->Token;
		$this->setSoapHeader();
	}

	private function setSoapHeader()
	{
		$header = [
			'UserID' => $this->userId,
			'Token' => $this->userToken
		];
		$soapVars = new \SoapVar($header, SOAP_ENC_OBJECT);
		$soapHeader = new \SoapHeader('Authentification', 'verifyingToken', $soapVars, false);
		$this->zendSoapClient
			->resetSoapInputHeaders()
			->addSoapInputHeader($soapHeader, true);
	}

	public function call($method, $params = [])
	{
		for ($i = 1; $i <= $this->maxCallTries; $i++)
		{
			try
			{
				return $this->zendSoapClient->call($method, [$params]);
			} catch (\Exception $e)
			{
				$this->handleCallException($e);
				if ($i == $this->maxCallTries)
				{
					throw $e;
				}
			}
		}
	}

	private function handleCallException(\Exception $e)
	{
		$message = $e->getMessage();
		$messageHandlers = [
			['message' => 'fetching http headers', 'sleep' => 5, 'auth' => false],
			['message' => 'cannot find parameter', 'sleep' => 5, 'auth' => false],
			['message' => 'looks like we got no XML document', 'sleep' => 5, 'auth' => false],
			['message' => 'bad gateway', 'sleep' => 10, 'auth' => false],
			['message' => 'service unavailable', 'sleep' => 10, 'auth' => false],
			['message' => 'too many requests', 'sleep' => 30, 'auth' => false],
		];
		foreach ($messageHandlers as $messageHandler)
		{
			if (preg_match('/' . $messageHandler['message'] . '/is', $message))
			{
				sleep($messageHandler['sleep']);
				if ($messageHandler['auth']) $this->auth();

				return;
			}
		}

		throw $e;
	}
}

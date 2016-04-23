<?php

namespace Plenty\Api\Client;

use Core\Config\Config\AppConfig;
use League\Flysystem\Exception;
use rock\cache\CacheInterface;
use Zend\Soap\Client;

class SoapClient implements ClientInterface {

    /** @var Client */
    private $zendSoapClient;
	/** @var CacheInterface */
	private $cache;
	/** @var AppConfig */
	private $appConfig;

	/**
	 * Soap constructor.
	 * @param AppConfig $appConfig
	 * @param CacheInterface $cache
	 */
    public function __construct(AppConfig $appConfig, CacheInterface $cache)
	{

		$this->appConfig = $appConfig;
		$this->cache = $cache;

		$this->zendSoapClient = new Client($appConfig->get('plenty/api/wsdl'));

		$this->authenticate();
	}


    protected function authenticate() {

		$this->setSoapHeader();

        try {
            $this->call('GetServerTime');
        } catch (\Exception $e) {

            if (preg_match('/(token required|invalid token)/is', $e->getMessage())) {
                $this->updateCredentials();

				$this->setSoapHeader();
            }

        }
    }


    protected function updateCredentials() {

        $result = $this->zendSoapClient->call('GetAuthentificationToken', [
            [
                'Username' => $this->appConfig->get('plenty/api/username'),
                'Userpass' => $this->appConfig->get('plenty/api/password')
            ]
		]);

		if (isset($result->Token) && $result->Token != '') {
			$this->cache->set('plenty.soap.token', (string)$result->Token, 86400);
			$this->cache->set('plenty.soap.user_id', (string)$result->UserID, 86400);
		} else {
			throw new \Exception('token could not be fetched (maybe call limit)');
		}

    }


	protected function setSoapHeader() {

		$header = [
			'UserID' => $this->cache->get('plenty.soap.user_id'),
			'Token' => $this->cache->get('plenty.soap.token')
		];

		$soapVars = new \SoapVar($header, SOAP_ENC_OBJECT);
		$soapHeader = new \SoapHeader('Authentification', 'verifyingToken', $soapVars, false);

		$this->zendSoapClient
			->resetSoapInputHeaders()
			->addSoapInputHeader($soapHeader, true);
	}


    public function call($method, $params = []) {

		$tries = 10;
		for ($i=0;$i<=$tries; $i++)
		{
			try
			{
				return $this->zendSoapClient->call($method, [$params]);
			} catch (\Exception $e)
			{
				$message = $e->getMessage();

				if ($i < 10)
				{
					$errorHandlings = [
						['message' => 'fetching http headers', 'sleep' => 5],
						['message' => 'cannot find parameter', 'sleep' => 5],
						['message' => 'looks like we got no XML document', 'sleep' => 5],
						['message' => 'bad gateway', 'sleep' => 10],
						['message' => 'service unavailable', 'sleep' => 10],
						['message' => 'too many requests', 'sleep' => 30],
					];

					$continue = false;
					foreach ($errorHandlings as $errorHandling)
					{
						if (preg_match('/' . $errorHandling['message'] . '/is', $message))
						{
							echo "ERROR - " . $errorHandling['message'] . "\n";
							if ($errorHandling['sleep'] > 0)
							{
								echo 'sleeping ' . $errorHandling['sleep'] . " seconds...\n";
								sleep($errorHandling['sleep']);
							}
							$continue = true;
						}
					}

					if ($continue)
					{
						continue;
					}

				}

				file_put_contents($this->appConfig->get('app/log_dir') . 'request.xml', ($this->zendSoapClient->getLastRequest()));
				file_put_contents($this->appConfig->get('app/log_dir') . 'response.xml', ($this->zendSoapClient->getLastResponse()));

				throw $e;
			}
		}
    }

}

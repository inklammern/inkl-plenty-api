<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\ClientInterface;

class OrderStatusService
{
	/** @var ClientInterface */
	private $client;

	/**
	 * OrderService constructor.
	 * @param ClientInterface $client
	 */
	public function __construct(ClientInterface $client)
	{
		$this->client = $client;
	}


	public function getAll()
	{
		$result = $this->client->call('GetOrderStatusList', ['Lang' => 'de']);

		print_r($result);
		exit;
	}

}

<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\RestClient;

class OrderService
{
	private $client;

	/**
	 * OrderService constructor.
	 * @param RestClient $client
	 */
	public function __construct(RestClient $client)
	{
		$this->client = $client;
	}

	public function saveStatus($orderId, $status)
	{
		return $this->client->put(sprintf('orders/%d', $orderId), [
			'statusId' => $status
		]);
	}


	public function getById($orderId)
	{

	}


	public function searchByStatus($status)
	{
		$result = $this->client->get('orders', [
			'statusFrom' => $status,
			'statusTo' => $status,
		]);

		$orders = [];
		if (isset($result->body->entries))
		{
			foreach ($result->body->entries as $order)
			{
				$orders[] = (array)$order;
			}
		}

		if (count($orders) > 0)
		{
			return $orders;
		}

		return null;
	}

}

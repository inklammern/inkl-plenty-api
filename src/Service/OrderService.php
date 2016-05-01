<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\ClientInterface;

class OrderService
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



	public function saveStatus($orderId, $status)
	{

		$result = $this->client->call('SetOrderStatus', [
			'OrderStatus' => [['OrderID' => $orderId, 'OrderStatus' => $status]]
		]);

		return $result;
	}


	public function searchByStatus($status)
	{
		$result = $this->client->call('SearchOrders', [
			'OrderStatus' => $status
		]);

		if (!isset($result->Success) || $result->Success != '1')
		{
			throw new \Exception('search orders failed');
		}

		if (!isset($result->Orders->item))
		{
			return [];
		}

		$resultOrders = $result->Orders->item;
		if (!is_array($resultOrders)) $resultOrders = [$resultOrders];

		$orders = [];
		foreach ($resultOrders as $item)
		{
			$orders[] = [
				'id' => (string)$item->OrderHead->OrderID,
				'external_id' => (string)$item->OrderHead->ExternalOrderID,
				'status' => (string)$item->OrderHead->OrderStatus
			];
		}

		return $orders;
	}

}

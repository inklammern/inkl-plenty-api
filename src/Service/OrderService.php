<?php

namespace Plenty\Api\Service;

use Plenty\Api\Client\ClientInterface;
use Plenty\Api\Entities\Order;

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


	/**
	 * @return Order[]
	 * @throws \Exception
	 */
	public function getShipping()
	{
		return $this->searchByStatus(6.7);
	}


	/**
	 * @return Order[]
	 * @throws \Exception
	 */
	public function getInvoiceShipping()
	{
		return $this->searchByStatus(6.8);
	}


	/**
	 * @return Order[]
	 * @throws \Exception
	 */
	public function getInvoiceDropShipping()
	{
		return $this->searchByStatus(6.9);
	}


	public function saveStatus($orderId, $status)
	{

		$result = $this->client->call('SetOrderStatus', [
			'OrderStatus' => [['OrderID' => $orderId, 'OrderStatus' => $status]]
		]);

		return $result;
	}


	private function searchByStatus($status)
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
			$orders[] = $this->hydrate($item);
		}

		return $orders;
	}


	private function hydrate($data) {

		$order = new Order();

		$order
			->setId((string)$data->OrderHead->OrderID)
			->setExternalId((string)$data->OrderHead->ExternalOrderID)
			->setStatus((string)$data->OrderHead->OrderStatus);

		return $order;
	}

}

<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\RestClient;

class OrderPropertyService
{
	const ID_EXTERNAL_ORDER_ID = 7;

	private $client;

	/**
	 * OrderService constructor.
	 * @param RestClient $client
	 */
	public function __construct(RestClient $client)
	{
		$this->client = $client;
	}

	public function getPropertyValue($orderId, $propertyId)
	{
		$result = $this->client->get(sprintf('orders/%d/properties/%d', $orderId, $propertyId));

		if (isset($result->body[0]))
		{
			return (string)$result->body[0]->value;
		}

		return null;
	}


}

<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\RestClient;

class CountriesOfDeliveryService
{
	/** @var RestClient */
	private $client;

	/**
	 * @param RestClient $client
	 */
	public function __construct(RestClient $client)
	{
		$this->client = $client;
	}

	public function getAll()
	{
		$result = $this->client->get('orders/shipping/countries');

		if ($result->code !== 200)
		{
			throw new \Exception('orders/shipping/countries failed');
		}

		$countries = [];
		if (isset($result->body))
		{
			foreach ($result->body as $country)
			{
				$countries[] = (array)$country;
			}
		}

		if (count($countries) > 0)
		{
			return $countries;
		}

		return null;
	}

}

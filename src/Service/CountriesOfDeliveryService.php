<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\ClientInterface;

class CountriesOfDeliveryService
{
	/** @var ClientInterface */
	private $client;

	/**
	 * PropertyGroupService constructor.
	 * @param ClientInterface $client
	 */
	public function __construct(ClientInterface $client)
	{
		$this->client = $client;
	}

	public function getAll()
	{
		$result = $this->client->call('GetCountriesOfDelivery');

		if (!isset($result->Success) || $result->Success != '1')
		{
			throw new \Exception('countries of delivery failed');
		}

		if (!isset($result->CountriesOfDelivery->item))
		{
			return null;
		}

		$countries = [];
		foreach ($result->CountriesOfDelivery->item as $country)
		{
			$countries[] = (array)$country;
		}

		return $countries;
	}

}

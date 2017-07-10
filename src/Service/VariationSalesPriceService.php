<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\RestClient;

class VariationSalesPriceService
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

	/**
	 * @param $itemId
	 * @param $itemVariationId
	 * @param $salesPriceId
	 * @return float
	 */
	public function getPrice($itemId, $itemVariationId, $salesPriceId)
	{
		$response = $this->client->get(sprintf('items/%d/variations/%d/variation_sales_prices/%d', $itemId, $itemVariationId, $salesPriceId));

		if (isset($response->body->price))
		{
			return (double)$response->body->price;
		}

		return null;
	}

	public function updatePrice($itemId, $itemVariationId, $salesPriceId, $price)
	{
		return $this->client->put(sprintf('items/%d/variations/%d/variation_sales_prices/%d', $itemId, $itemVariationId, $salesPriceId), [
			'price' => $price
		]);
	}

}

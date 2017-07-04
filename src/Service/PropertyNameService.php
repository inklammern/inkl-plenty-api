<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\RestClient;
use Psr\Log\LoggerInterface;

class PropertyNameService
{
	/** @var LoggerInterface */
	private $logger;
	/** @var RestClient */
	private $client;

	/**
	 * PropertyService constructor.
	 * @param RestClient $client
	 * @param LoggerInterface $logger
	 */
	public function __construct(RestClient $client, LoggerInterface $logger)
	{
		$this->logger = $logger;
		$this->client = $client;
	}

	public function getByPropertyId($propertyId)
	{
		$result = $this->client->get(sprintf('items/properties/%d/names', $propertyId));

		if ($result->code !== 200)
		{
			throw new \Exception('property names failed');
		}

		$propertyNames = [];
		if (isset($result->body))
		{
			foreach ($result->body as $propertyName)
			{
				$propertyNames[] = (array)$propertyName;
			}
		}

		if (count($propertyNames) > 0)
		{
			return $propertyNames;
		}

		return null;
	}

}

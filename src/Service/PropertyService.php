<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\RestClient;
use Psr\Log\LoggerInterface;

class PropertyService
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

	public function getAll($page = 1, $fromUpdatedAt = null)
	{
		$result = $this->client->get('items/properties', [
			'page' => $page,
			'itemsPerPage' => 1000,
			'updatedAt' => $fromUpdatedAt
		]);

		if ($result->code !== 200)
		{
			throw new \Exception('properties failed');
		}


		$properties = [];
		if (isset($result->body->entries))
		{
			foreach ($result->body->entries as $property)
			{
				$properties[] = (array)$property;
			}
		}

		if (count($properties) > 0)
		{
			return $properties;
		}

		return null;
	}

}

<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\RestClient;
use Psr\Log\LoggerInterface;

class PropertyGroupService
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

	public function getAll($page = 1)
	{
		$result = $this->client->get('items/property_groups', [
			'page' => $page,
			'itemsPerPage' => 1000
		]);

		if ($result->code !== 200)
		{
			throw new \Exception('property groups failed');
		}


		$propertyGroups = [];
		if (isset($result->body->entries))
		{
			foreach ($result->body->entries as $propertyGroup)
			{
				$propertyGroups[] = (array)$propertyGroup;
			}
		}

		if (count($propertyGroups) > 0)
		{
			return $propertyGroups;
		}

		return null;
	}

}

<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\RestClient;
use Psr\Log\LoggerInterface;

class PropertyGroupNameService
{
	/** @var LoggerInterface */
	private $logger;
	/** @var RestClient */
	private $client;

	/**
	 * PropertyGroupService constructor.
	 * @param RestClient $client
	 * @param LoggerInterface $logger
	 */
	public function __construct(RestClient $client, LoggerInterface $logger)
	{
		$this->logger = $logger;
		$this->client = $client;
	}

	public function getByPropertyGroupId($propertyGroupId)
	{
		$result = $this->client->get(sprintf('items/property_groups/%d/names', $propertyGroupId));

		if ($result->code !== 200)
		{
			throw new \Exception('property group names failed');
		}

		$propertyGroupNames = [];
		if (isset($result->body))
		{
			foreach ($result->body as $propertyGroupName)
			{
				$propertyGroupNames[] = (array)$propertyGroupName;
			}
		}

		if (count($propertyGroupNames) > 0)
		{
			return $propertyGroupNames;
		}

		return null;
	}

}

<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class PropertyService
{
	/** @var ClientInterface */
	private $client;
	/** @var LoggerInterface */
	private $logger;

	/**
	 * PropertyService constructor.
	 * @param ClientInterface $client
	 * @param LoggerInterface $logger
	 */
	public function __construct(ClientInterface $client, LoggerInterface $logger)
	{
		$this->client = $client;
		$this->logger = $logger;
	}

	public function getPage($page = 0)
	{
		$result = $this->client->call('GetProperties', [
			'Page' => $page
		]);

		if (!isset($result->Success) || $result->Success != 1)
		{
			throw new \Exception('properties failed');
		}

		if (!isset($result->Properties->item))
		{
			return null;
		}

		$properties = [];
		foreach ($result->Properties->item as $item)
		{
			$properties[] = (array)$item;
		}

		return $properties;
	}

}

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


	public function getAll()
	{
		$properties = [];

		$page = 0;
		$countPages = 1;
		while ($page < $countPages)
		{
			$result = $this->client->call('GetProperties', ['Page' => $page]);

			$page++;

			$this->logger->debug(sprintf('getting property page %d', $page));

			if (!isset($result->Success) || $result->Success != 1 || !isset($result->Properties->item))
			{
				break;
			}

			$countPages = $result->Pages;

			foreach ($result->Properties->item as $item)
			{
				$item = (array)$item;

				$id = $item['PropertyID'];
				$lang = strtolower($item['Lang']);

				if (!($item['PropertyGroupID'] > 0)) continue;

				if ($lang != 'de' && $lang != 'fr' && $lang != 'es')
				{
					continue;
				}

				if (!isset($properties[$id]))
				{
					$properties[$id] = [
						'id' => $id,
						'property_group_id' => $item['PropertyGroupID'],
						'name' => $item['PropertyBackendName'],
						'lang' => []
					];
				}

				$properties[$id]['lang'][$lang] = [
					'name' => $item['PropertyFrontendName']
				];

			}
		}

		return $properties;
	}

}

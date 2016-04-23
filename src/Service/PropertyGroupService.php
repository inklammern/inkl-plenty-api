<?php

namespace Plenty\Api\Service;

use Plenty\Api\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use rock\cache\CacheInterface;

class PropertyGroupService
{
	/** @var ClientInterface */
	private $client;
	/** @var CacheInterface */
	private $cache;
	/** @var PropertyService */
	private $propertyService;
	/** @var LoggerInterface */
	private $log;

	/**
	 * PropertyGroupService constructor.
	 * @param ClientInterface $client
	 * @param CacheInterface $cache
	 * @param PropertyService $propertyService
	 * @param LoggerInterface $log
	 */
	public function __construct(ClientInterface $client, CacheInterface $cache, PropertyService $propertyService, LoggerInterface $log)
	{
		$this->client = $client;
		$this->cache = $cache;
		$this->propertyService = $propertyService;
		$this->log = $log;
	}


	public function getAll()
	{
		$this->log->debug('getting property groups');

		$cacheKey = __CLASS__ . __METHOD__;
		if ($this->cache->exists($cacheKey))
		{
			$propertyGroups = $this->cache->get($cacheKey);

			$this->log->debug(sprintf('found %d property groups (from cache)', count($propertyGroups)));

			return $propertyGroups;
		}

		$result = $this->client->call('GetPropertyGroups');

		if (!isset($result->Success) || $result->Success != '1' || !isset($result->PropertyGroups->item))
		{
			$this->log->debug('failed!');
			throw new \Exception('property groups failed');
		}

		$propertyGroups = [];
		foreach ($result->PropertyGroups->item as $propertyGroupData)
		{
			$propertyGroupData = (array)$propertyGroupData;

			$id = $propertyGroupData['PropertyGroupID'];
			$lang = $propertyGroupData['Lang'];
			$backendName = $propertyGroupData['BackendName'];
			$frontendName = $propertyGroupData['FrontendName'];

			if (!isset($propertyGroups[$id]))
			{
				$propertyGroups[$id] = [
					'id' => $id,
					'name' => $backendName,
					'properties' => [],
					'lang' => []
				];
			}

			$propertyGroups[$id]['lang'][$lang] = [
				'name' => $frontendName
			];

		}

		$this->log->debug(sprintf('found %d property groups', count($propertyGroups)));

		$this->cache->set($cacheKey, $propertyGroups, 3600);

		return $propertyGroups;
	}


	public function getAllWithProperties()
	{

		$this->log->debug('getting property groups with properties');

		$propertyGroups = $this->getAll();
		$properties = $this->propertyService->getAll();

		foreach ($properties as $property)
		{
			$propertyGroupId = $property['property_group_id'];

			if (isset($propertyGroups[$propertyGroupId]))
			{
				$propertyGroups[$propertyGroupId]['properties'][] = $property;
			}
		}

		$this->log->debug(sprintf('found %d property groups with properties', count($propertyGroups)));

		return $propertyGroups;
	}

}
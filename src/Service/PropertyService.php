<?php

namespace Plenty\Api\Service;

use Plenty\Api\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use rock\cache\CacheInterface;

class PropertyService
{
	/** @var ClientInterface */
	private $client;
	/** @var CacheInterface */
	private $cache;
	/** @var LoggerInterface */
	private $log;

	/**
	 * PropertyService constructor.
	 * @param ClientInterface $client
	 * @param CacheInterface $cache
	 * @param LoggerInterface $log
	 */
	public function __construct(ClientInterface $client, CacheInterface $cache, LoggerInterface $log)
	{
		$this->client = $client;
		$this->cache = $cache;
		$this->log = $log;
	}


	public function getAll()
	{
		$this->log->debug('getting properties');

		$cacheKey = __CLASS__ . __METHOD__;
		if ($this->cache->exists($cacheKey))
		{
			$properties = $this->cache->get($cacheKey);

			$this->log->debug(sprintf('found %d properties (from cache)', count($properties)));

			return $properties;
		}

		$properties = [];

		$page = 0;
		$countPages = 1;
		while ($page < $countPages)
		{

			$this->log->debug(sprintf('getting %s', ($page == 0 ? 'first page' : ($page+1) . '/' . $countPages)));

			$result = $this->client->call('GetProperties', ['Page' => $page]);
			$page++;

			if (!isset($result->Success) || $result->Success != 1 || !isset($result->Properties->item))
			{
				break;
			}

			$countPages = $result->Pages;

			foreach ($result->Properties->item as $item)
			{

				$item = (array)$item;

				$id = $item['PropertyID'];
				$lang = $item['Lang'];

				if ($lang != 'de' && $lang != 'fr')
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

		$this->cache->set($cacheKey, $properties, 3600);

		$this->log->debug(sprintf('found %d properties', count($properties)));

		return $properties;
	}

}
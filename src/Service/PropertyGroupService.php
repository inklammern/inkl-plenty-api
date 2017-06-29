<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\ClientInterface;

class PropertyGroupService
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

	public function getPage($page = 0)
	{
		$result = $this->client->call('GetPropertyGroups', [
			'Page' => $page
		]);

		if (!isset($result->Success) || $result->Success != '1')
		{
			throw new \Exception('property groups failed');
		}

		if (!isset($result->PropertyGroups->item))
		{
			return null;
		}

		$propertyGroups = [];
		foreach ($result->PropertyGroups->item as $propertyGroupData)
		{
			$propertyGroups[] = (array)$propertyGroupData;
		}

		return $propertyGroups;
	}

}

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


	public function getAll()
	{

		$result = $this->client->call('GetPropertyGroups');

		if (!isset($result->Success) || $result->Success != '1' || !isset($result->PropertyGroups->item))
		{
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
					'lang' => []
				];
			}

			$propertyGroups[$id]['lang'][$lang] = [
				'name' => $frontendName
			];

		}

		return $propertyGroups;
	}

}

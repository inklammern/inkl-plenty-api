<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\ClientInterface;

class DynamicFormatService
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


    public function getSimpleList()
    {

        $result = $this->client->call('GetDynamicFormats');

        if (!isset($result->Success) || $result->Success != '1' || !isset($result->DynamicFormats->item))
        {
            throw new \Exception('GetDynamicFormats failed');
        }

        $dynamicFormats = [];
        foreach ($result->DynamicFormats->item as $dynamicFormat)
        {
            $dynamicFormat = (array)$dynamicFormat;

            $dynamicFormats[$dynamicFormat['ID']] = $dynamicFormat['UserName'];
        }

        return $dynamicFormats;
    }

}

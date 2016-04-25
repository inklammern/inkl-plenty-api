<?php

namespace Inkl\PlentyApi\Service;

use Inkl\PlentyApi\Client\ClientInterface;
use Core\Csv\Helper\CsvHelper;

class DynamicExportService {

    /** @var ClientInterface */
    private $client;
    /** @var CsvHelper */
    private $csvHelper;

    /**
     * DynamicExportService constructor.
     * @param ClientInterface $client
     * @param CsvHelper $csvHelper
     */
    public function __construct(ClientInterface $client, CsvHelper $csvHelper) {
        $this->client = $client;
        $this->csvHelper = $csvHelper;
    }


    public function exportFormat($formatId, $formatName, $offset = 0, $rowCount = 1000) {

        $result = $this->client->call('GetDynamicExport', [
            'FormatID' => $formatId,
            'FormatName' => $formatName,
            'Offset' => $offset,
            'RowCount' => $rowCount
        ]);

        if (!isset($result->Success) || $result->Success != '1' || !isset($result->Content->item)) throw new \Exception('dynamic export failed');

        $content = '';
        foreach ($result->Content->item as $item) {

            if (!isset($item->Value)) continue;

            $content .= (string)$item->Value . "\n";
        }

        return $this->csvHelper->stringToArray($content, ';');
    }

}

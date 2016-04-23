<?php

namespace Plenty\Api\Service;

use League\Flysystem\Exception;
use Plenty\Api\Client\ClientInterface;

class DynamicImportService {

	/** @var ClientInterface */
	private $client;

	/**
	 * DynamicExportService constructor.
	 * @param ClientInterface $client
	 */
	public function __construct(ClientInterface $client) {
		$this->client = $client;
	}


	public function importFormat($formatId, $formatName, array $lines) {

		$content = [];
		foreach ($lines as $line) {
			$content[] = ['Value' => $line];
		}

		$result = $this->client->call('SetDynamicImport', [
				'FormatID' => $formatId,
				'FormatName' => $formatName,
				'Delimiter' => 3,
				'Content' => $content,
				'OnlyMatching' => true
		]);

		if (!isset($result->Success) || $result->Success != '1') throw new \Exception('dynamic import failed');

		return true;
	}

}
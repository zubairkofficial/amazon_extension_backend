<?php

namespace App\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AmazonScraper
{
    private $apiToken;
    private $asins;
    private $actor;
    private $client;

    /**
     * Create a new class instance.
     *
     * @param string $apiToken The API key used for authentication.
     * @param string $asins The ASINS to scrape.
     * @param string $actor The actor for the Apify task.
     */
    public function __construct(string $apiToken, string $asins, string $actor)
    {
        $this->apiToken = $apiToken;
        $this->asins = $asins;
        $this->actor = $actor;
        $this->client = new Client([
            'base_uri' => 'https://api.apify.com',
            'timeout'  => 30.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken,
            ],
        ]);
    }

    public function runApifyActor()
    {
        // Prepare input JSON
        $inputJson = [
            "asins" => [$this->asins],
            "useCaptchaSolver" => true,
            "proxyConfiguration" => [
                "useApifyProxy" => true,
                "apifyProxyGroups" => [
                    "RESIDENTIAL"
                ]
            ]
        ];

        try {
            $response = $this->client->post("/v2/acts/{$this->actor}/runs", ['json' => $inputJson]);
            sleep(10); // Pause execution to wait for the actor to run

            if ($response->getStatusCode() === 201) {
                $responseData = json_decode($response->getBody()->getContents(), true);

                $keyValueStoreId = $responseData['data']['defaultKeyValueStoreId'];

                $keyValueStoreResponse = $this->client->get("/v2/key-value-stores/{$keyValueStoreId}/records/INPUT");
                sleep(15);
                
                if ($keyValueStoreResponse->getStatusCode() === 200) {
                    $datasetId = $responseData['data']['defaultDatasetId'];
                    $datasetResponse = $this->client->get("/v2/datasets/{$datasetId}/items");

                    sleep(5);
                    if ($datasetResponse->getStatusCode() === 200) {
                        $datasetData = json_decode($datasetResponse->getBody()->getContents(), true);
                        return $datasetData;
                    }
                }
            }
        } catch (GuzzleException $e) {
            // Handle the exception or log it
            throw $e;
        }

        // Return null or an appropriate response in case of failure
        return null;
    }
}

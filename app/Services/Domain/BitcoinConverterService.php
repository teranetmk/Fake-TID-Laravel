<?php

/**
 * FakeTids.su
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

namespace App\Services\Domain;

use Throwable;
use GuzzleHttp\Client;

class BitcoinConverterService
{
    const BASE_URL = "https://blockchain.info/";
    const TOBTC_ENDPOINT = "tobtc?currency=%s&value=%f";

    /** @var \GuzzleHttp\Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = new Client([
            'base_uri'      => self::BASE_URL,
            'debug'         => false,
            'http_errors'   => false,
        ]);
    }

    public function toBitcoin(float $amount, string $currency = 'eur'): float
    {
        try {
            $response = $this->client->request('GET', sprintf(self::TOBTC_ENDPOINT, $currency, $amount), 
                [
                    'headers'   => [
                        'Accept'        => 'application/json',
                    ]
                ]
            );

            $data = json_decode($response->getBody(), true);
            
            return $data ?? 0;
        } catch (Throwable $e) {
            return 0;
        }
    }
}
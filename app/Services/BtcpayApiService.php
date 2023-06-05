<?php

namespace App\Services;

use GuzzleHttp\Client;

class BtcpayApiService
{
    private $client;

    public function __construct()
    {
        $credentials = base64_encode('skypefreelancer@gmail.com:mBIJebrkVFkCguHhfVWaFOW3x');
        $this->client = new Client([
            'base_uri' => 'https://btcpay0.voltageapp.io/api/v1/stores/3aMQ28j3f5NiFBTD2XVE6mPt2Ts1i7WePADXLGtpQqpg/',
            'headers' => [
                'Authorization' => ['Basic '.$credentials],
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function getAllinvoices(){
        $response = $this->client->get('invoices');
        
        return json_decode($response->getBody()->getContents(), true);
    }
    public function createInvoice(array $data)
    {
        $response = $this->client->post('invoices', [
            'json' => $data,
        ]);
        
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getInvoice($invoiceId)
    {
        $response = $this->client->get("invoices/{$invoiceId}");

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getWalletAddress(){
       
        $response = $this->client->get("payment-methods/onchain/BTC/wallet/address");
   
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getCustodianAccount(){
        $response = $this->client->get("payment-methods/onchain/BTC/wallet/transactions");
   
        return json_decode($response->getBody()->getContents(), true);
    }
}
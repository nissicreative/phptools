<?php
namespace Nissi\Ecommerce\Gateways;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class CardConnect
{
    protected $client;
    protected $username;
    protected $password;
    protected $baseUri;
    protected $authstring;

    public function __construct($url, $username = null, $password = null)
    {
        $this->username   = $username;
        $this->password   = $password;
        $this->baseUri    = $url . '/cardconnect/rest/';
        $this->authstring = 'Basic ' . base64_encode("$username:$password");

        $this->client = new Client([
            'base_uri' => $url . '/cardconnect/rest/',
            'headers'  => [
                'Content-Type'  => 'application/json',
                'Authorization' => $this->authstring,
            ],
        ]);
    }

    /**
     * Test the gateway credentials.
     */
    public function test()
    {
        return $this->client->request('GET');
    }

    /**
     * Authorize transaction.
     */
    public function authorize($requestData = [])
    {
        $defaults = [
            'currency' => 'USD',
            'country'  => 'US',
            'tokenize' => 'Y',
        ];

        $requestData += $defaults;

        $requestBody = json_encode($requestData);

        $request = new Request('PUT', $this->baseUri . 'auth');

        $res = $this->client->send($request, [
            'body' => $requestBody,
        ]);

        $responseBody = (string) $res->getBody(); // JSON String

        return json_decode($responseBody);
    }

    /**
     * Add a new customer profile.
     */
    public function createProfile($requestData = [])
    {
        $defaults = [
            'profileupdate' => 'N',
            'defaultacct'   => 'Y',
        ];

        $requestData += $defaults;

        $requestBody = json_encode($requestData);

        $request = new Request('PUT', $this->baseUri . 'profile');

        $res = $this->client->send($request, [
            'body' => $requestBody,
        ]);

        $responseBody = (string) $res->getBody(); // JSON String

        return json_decode($responseBody);
    }

    /**
     * Request a refund.
     */
    public function refund($requestData = [])
    {
        $defaults = [];

        $requestData += $defaults;

        $requestBody = json_encode($requestData);

        $request = new Request('PUT', $this->baseUri . 'refund');

        $res = $this->client->send($request, [
            'body' => $requestBody,
        ]);

        $responseBody = (string) $res->getBody(); // JSON String

        return json_decode($responseBody);
    }

    /**
     * Format an expiry date.
     */
    public static function formatExpiry($year = 0, $month = 0, $format = 'my')
    {
        try {
            $expiry = Carbon::createFromDate($year, $month, 1);
            return $expiry->format($format);
        } catch (\Exception $e) {
            return '';
        }
    }
}

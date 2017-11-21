<?php

namespace ETelford\Rachio;

use ETelford\Rachio\Rachio;
use GuzzleHttp\Client as HttpClient;

abstract class Client
{
    /**
     * The Rachio instance
     *
     * @var \ETelford\Rachio\Rachio
     */
    protected $rachio;

    /**
     * The Guzzle HTTP client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var string The Rachio API end point
     */
    protected $baseUri = 'https://api.rach.io/1/public/';

    /**
     * Init
     *
     * @param Rachio $rachio
     */
    public function __construct(Rachio $rachio)
    {
        $this->rachio = $rachio;
        $this->client = $this->createClient();
    }

    /**
     * Create the Guzzle HTTP Client
     *
     * @return \GuzzleHttp\Client
     */
    protected function createClient()
    {
        return new HttpClient(['base_uri' => $this->baseUri]);
    }

    /**
     * Get the person ID for the Rachio a
     *
     * Every request must start by getting person:id associated with the
     * account.
     *
     * https://api.rach.io/1/public/person/info
     *
     * @return statusCode The HTTP status code
     */
    protected function authorize()
    {
        $request = $this->client->get('person/info', $this->setHeaders());

        return $this->sanitize($request)->id;
    }

    /**
     * Set the headers for the request
     *
     * @return array
     */
    protected function setHeaders()
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->rachio->getApiKey(),
                'Content-Type' => 'application/json'
            ]
        ];
    }

    /**
     * Set the body for the request
     *
     * @return array
     */
    protected function setBody(array $body)
    {
        return ['body' => json_encode($body)];
    }

    /**
     * Convert the response to JSON
     * @param  Rachio Response $data A JSON string response from the Rachio API
     *
     * @return \StdClass
     */
    protected function sanitize($request)
    {
        return json_decode(
            $request->getBody()->getContents()
        );
    }
}

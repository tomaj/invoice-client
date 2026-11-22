<?php

declare(strict_types=1);

namespace Invoice\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Invoice\Exception;

class GuzzleTransport implements TransportInterface
{
    public const TIMEOUT = 10;

    private ClientInterface $client;

    public function __construct(?ClientInterface $client = null)
    {
        $this->client = $client ?? new Client([
            'timeout' => self::TIMEOUT,
        ]);
    }

    /**
     * Server errors (5xx) are returned as a Result with the response body, so the caller can read the error message.
     *
     * @throws GuzzleException on network errors and 4xx responses
     * @throws Exception for unsupported HTTP methods
     */
    public function call(string $url, string $apiKey, ?string $payload, string $method = 'post'): ?Result
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
            ],
        ];

        if ($payload !== null && $payload !== '') {
            $options['body'] = $payload;
        }

        if ($method === 'post') {
            try {
                $response = $this->client->request('POST', $url, $options);
            } catch (ServerException $error) {
                $response = $error->getResponse();
            }

            return new Result($response->getStatusCode(), (string) $response->getBody());
        }
        throw new Exception(sprintf("HTTP method '%s' is not supported by %s", $method, self::class));
    }
}

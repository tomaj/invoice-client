<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Tomaj\InvoiceClient\Exception\TransportException;

/**
 * Sends the requests with Guzzle. The default transport of InvoiceApi.
 */
final readonly class GuzzleTransport implements TransportInterface
{
    /** Timeout of the default client, in seconds. */
    public const int TIMEOUT = 10;

    private ClientInterface $client;

    /**
     * @param ClientInterface|null $client your own configured client; without one a client with a 10 second timeout is created
     */
    public function __construct(?ClientInterface $client = null)
    {
        $this->client = $client ?? new Client([
            'timeout' => self::TIMEOUT,
        ]);
    }

    /**
     * A body is sent with Content-Type: application/json. Every HTTP response is returned as a Result,
     * 4xx and 5xx included, so the caller can read the API error.
     */
    #[\Override]
    public function call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): Result
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiToken,
            ],
            'http_errors' => false,
        ];

        if (($payload ?? '') !== '') {
            // the payload is JSON already: send it as is (Guzzle's "json" option would encode it again)
            $options['headers']['Content-Type'] = 'application/json';
            $options['body'] = $payload;
        }

        try {
            $response = $this->client->request($method->value, $url, $options);
        } catch (GuzzleException $e) {
            throw new TransportException(sprintf('%s %s failed: %s', $method->value, $url, $e->getMessage()), 0, $e);
        }

        return new Result($response->getStatusCode(), (string) $response->getBody());
    }
}

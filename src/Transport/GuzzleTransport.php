<?php

declare(strict_types=1);

namespace Invoice\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Invoice\Exception;

class GuzzleTransport implements TransportInterface
{
    public const TIMEOUT = 10;

    /**
     * @throws GuzzleException on network errors and 4xx responses
     * @throws Exception for unsupported HTTP methods
     */
    public function call(string $url, string $apiKey, ?string $payload, string $method = 'post'): ?Result
    {
        $headers = [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
            ],
        ];

        if ($payload !== null && $payload !== '') {
            $headers['body'] = $payload;
        }

        $client = new Client([
            'timeout' => self::TIMEOUT,
        ]);

        if ($method === 'post') {
            try {
                $response = $client->post($url, $headers);
            } catch (ServerException $error) {
                $body = (string) $error->getResponse()->getBody();
                echo $body . "\n";
                return new Result(500, $body);
            }

            return new Result($response->getStatusCode(), (string) $response->getBody());
        }
        throw new Exception('error - treba osetri');
    }
}

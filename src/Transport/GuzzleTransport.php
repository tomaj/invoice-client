<?php

namespace Invoice\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Symfony\Component\Config\Definition\Exception\Exception;

class GuzzleTransport implements TransportInterface
{
    const TIMEOUT = 10;

    public function call($url, $apiKey, $payload, $method = 'post')
    {
        $headers = [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
            ],
        ];

        if ($payload) {
            $headers['body'] = $payload;
        }

        $client = new Client([
            'timeout' => self::TIMEOUT,
        ]);

        if ($method == 'post') {
            try {
                $response = $client->post($url, $headers);
            } catch (ServerException $error) {
                echo $error->getResponse()->getBody() . "\n";
                return new Result(500, $error->getResponse()->getBody());
            }

            return new Result($response->getStatusCode(), $response->getBody());
        }
        throw new Exception('error - treba osetri');
    }
}

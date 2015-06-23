<?php

namespace Invoice\Transport;

interface TransportInterface
{
    /**
     * @param string $url
     * @param string $apiKey
     * @param string $payload
     * @param string $method
     * @return Result
     */
    public function call($url, $apiKey, $payload, $method = 'post');
}

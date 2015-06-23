<?php

namespace Invoice\Transport;

class NullTransport implements TransportInterface
{
    public function call($url, $apiKey, $payload, $method = 'post')
    {
        return null;
    }
}

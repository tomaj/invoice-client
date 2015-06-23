<?php

namespace Invoice\Transport;

class TestTransport implements TransportInterface
{
    private $response;

    private $url;

    private $apikey;

    private $payload;

    private $method;

    public function setResponse(Result $response)
    {
        $this->response = $response;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getApiKey()
    {
        return $this->apikey;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function call($url, $apiKey, $payload, $method = 'post')
    {
        $this->url = $url;
        $this->apikey = $apiKey;
        $this->payload = $payload;
        $this->method = $method;

        return $this->response;
    }
}

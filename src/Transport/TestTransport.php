<?php

declare(strict_types=1);

namespace Invoice\Transport;

class TestTransport implements TransportInterface
{
    private ?Result $response = null;

    private ?string $url = null;

    private ?string $apikey = null;

    private ?string $payload = null;

    private ?string $method = null;

    public function setResponse(Result $response): void
    {
        $this->response = $response;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getApiKey(): ?string
    {
        return $this->apikey;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function call(string $url, string $apiKey, ?string $payload, string $method = 'post'): ?Result
    {
        $this->url = $url;
        $this->apikey = $apiKey;
        $this->payload = $payload;
        $this->method = $method;

        return $this->response;
    }
}

<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Transport;

/**
 * A transport for your tests: records the requests and returns the responses you queue.
 *
 * Without a queued response, call() returns null, like NullTransport.
 */
final class TestTransport implements TransportInterface
{
    /** @var list<Result> */
    private array $responses = [];

    /** @var list<array{method: HttpMethod, url: string, apiToken: string, payload: string|null}> */
    private array $requests = [];

    /**
     * Queues a response for the next call.
     */
    public function addResponse(Result $response): self
    {
        $this->responses[] = $response;
        return $this;
    }

    /**
     * Every request sent so far, oldest first.
     *
     * @return list<array{method: HttpMethod, url: string, apiToken: string, payload: string|null}>
     */
    public function getRequests(): array
    {
        return $this->requests;
    }

    /**
     * The last request, or null before the first one.
     *
     * @return array{method: HttpMethod, url: string, apiToken: string, payload: string|null}|null
     */
    public function getLastRequest(): ?array
    {
        return $this->requests === [] ? null : $this->requests[array_key_last($this->requests)];
    }

    #[\Override]
    public function call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result
    {
        $this->requests[] = ['method' => $method, 'url' => $url, 'apiToken' => $apiToken, 'payload' => $payload];

        return array_shift($this->responses);
    }
}

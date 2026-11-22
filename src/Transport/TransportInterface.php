<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Transport;

use Tomaj\InvoiceClient\Exception\TransportException;

interface TransportInterface
{
    /**
     * Sends one request to the API and returns the HTTP response, including 4xx and 5xx responses.
     *
     * @param string $apiToken sent as "Authorization: Bearer <token>"
     * @param string|null $payload JSON request body, null for none
     * @return Result|null null when the transport does not send anything (NullTransport)
     * @throws TransportException when there is no HTTP response (connection error, timeout, ...)
     */
    public function call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result;
}

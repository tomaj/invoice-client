<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Transport;

/**
 * Sends nothing. Every API call returns a response with status code 0 and isOk() === false.
 */
final class NullTransport implements TransportInterface
{
    #[\Override]
    public function call(HttpMethod $method, string $url, string $apiToken, ?string $payload = null): ?Result
    {
        return null;
    }
}

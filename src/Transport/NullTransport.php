<?php

declare(strict_types=1);

namespace Invoice\Transport;

class NullTransport implements TransportInterface
{
    public function call(string $url, string $apiKey, ?string $payload, string $method = 'post'): ?Result
    {
        return null;
    }
}

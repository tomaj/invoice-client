<?php

declare(strict_types=1);

namespace Invoice\Transport;

interface TransportInterface
{
    public function call(string $url, string $apiKey, ?string $payload, string $method = 'post'): ?Result;
}

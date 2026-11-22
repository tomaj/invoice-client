<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Transport;

/**
 * The HTTP status code and body of one API response.
 */
final readonly class Result
{
    public function __construct(
        private int $code,
        private string $body,
    ) {}

    public function getCode(): int
    {
        return $this->code;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}

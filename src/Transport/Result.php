<?php

declare(strict_types=1);

namespace Invoice\Transport;

class Result
{
    private int $code;

    private string $body;

    public function __construct(int $code, string $body)
    {
        $this->code = $code;
        $this->body = $body;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}

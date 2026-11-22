<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Response;

use Tomaj\InvoiceClient\Transport\Result;

/**
 * Shared implementation of ResponseInterface.
 *
 * @internal
 */
trait ResponseTrait
{
    private int $statusCode = 0;

    private string $body = '';

    /** @var array<mixed> */
    private array $data = [];

    private ?string $errorMessage = null;

    private function parse(?Result $result): void
    {
        $this->statusCode = $result?->getCode() ?? 0;
        $this->body = $result?->getBody() ?? '';

        $data = json_decode($this->body, true);
        $this->data = is_array($data) && !array_is_list($data) ? $data : [];
        $this->errorMessage = $this->findError($result);
    }

    private function findError(?Result $result): ?string
    {
        if (!$result instanceof Result) {
            return 'No request was sent';
        }
        if ($this->data === []) {
            return sprintf('Cannot parse response from server (HTTP %d)', $this->statusCode);
        }
        $ok = ($this->data['status'] ?? null) === 'ok';
        if ($ok && $this->statusCode >= 200 && $this->statusCode < 300) {
            return null;
        }
        $message = $this->data['message'] ?? null;
        if (is_string($message) && $message !== '') {
            return $message;
        }

        return $ok ? sprintf('Unexpected HTTP status %d', $this->statusCode) : 'Unknown error';
    }

    public function isOk(): bool
    {
        return $this->errorMessage === null;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    private function stringOrNull(string $key): ?string
    {
        $value = $this->data[$key] ?? null;
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value)) {
            return (string) $value;
        }

        return null;
    }
}

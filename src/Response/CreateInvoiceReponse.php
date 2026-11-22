<?php

declare(strict_types=1);

namespace Invoice\Response;

use Invoice\Transport\Result;

class CreateInvoiceResponse implements ResponseInterface
{
    private ?string $id = null;

    private ?string $downloadPdfUrl = null;

    private ?string $htmlUrl = null;

    private string|false $errorMessage = false;

    public function __construct(Result $transportResult)
    {
        $result = json_decode($transportResult->getBody(), true);
        if (!is_array($result) || $result === []) {
            $this->errorMessage = 'Cannot parse response from server';
            return;
        }

        if (($result['status'] ?? null) !== 'ok') {
            $message = $result['message'] ?? null;
            $this->errorMessage = is_string($message) ? $message : 'Unknown error';
            return;
        }

        $this->htmlUrl = self::stringOrNull($result['html_url'] ?? null);
        $this->downloadPdfUrl = self::stringOrNull($result['download_pdf_url'] ?? null);
        $this->id = self::stringOrNull($result['id'] ?? null);
    }

    public function isOk(): bool
    {
        return $this->errorMessage === false;
    }

    public function getErrorMessage(): string|false
    {
        return $this->errorMessage;
    }

    public function getHtmlUrl(): ?string
    {
        return $this->htmlUrl;
    }

    public function getDownloadUrl(): ?string
    {
        return $this->downloadPdfUrl;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value)) {
            return (string) $value;
        }

        return null;
    }
}

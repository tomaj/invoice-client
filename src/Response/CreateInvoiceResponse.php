<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Response;

use Tomaj\InvoiceClient\Transport\Result;

/**
 * Response of InvoiceApi::createInvoice(): the id and URLs of the new invoice.
 */
final class CreateInvoiceResponse implements ResponseInterface
{
    use ResponseTrait;

    public function __construct(?Result $result)
    {
        $this->parse($result);
    }

    /**
     * The id of the created invoice, null when the request failed.
     */
    public function getId(): ?string
    {
        return $this->isOk() ? $this->stringOrNull('id') : null;
    }

    /**
     * URL of the HTML version of the invoice.
     */
    public function getHtmlUrl(): ?string
    {
        return $this->isOk() ? $this->stringOrNull('html_url') : null;
    }

    /**
     * URL to download the PDF of the invoice.
     */
    public function getDownloadUrl(): ?string
    {
        return $this->isOk() ? $this->stringOrNull('download_pdf_url') : null;
    }
}

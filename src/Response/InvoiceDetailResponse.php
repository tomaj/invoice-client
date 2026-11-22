<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Response;

use Tomaj\InvoiceClient\Transport\Result;

/**
 * Response of InvoiceApi::getInvoice(). getData() holds the invoice as the API returned it.
 */
final class InvoiceDetailResponse implements ResponseInterface
{
    use ResponseTrait;

    public function __construct(?Result $result)
    {
        $this->parse($result);
    }
}

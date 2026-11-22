<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Response;

use Tomaj\InvoiceClient\Transport\Result;

/**
 * Response of InvoiceApi::deleteInvoice(). isOk() tells whether the invoice was deleted.
 */
final class DeleteInvoiceResponse implements ResponseInterface
{
    use ResponseTrait;

    public function __construct(?Result $result)
    {
        $this->parse($result);
    }
}

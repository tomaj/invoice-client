<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Invoice;

/**
 * The issuer of an invoice is the account the API token belongs to, so the API takes no issuer data:
 * an Issuer has no fields and is not sent.
 */
final class Issuer
{
    /**
     * @param array<mixed> $data ignored
     */
    public static function fromArray(array $data): Issuer
    {
        return new Issuer();
    }
}

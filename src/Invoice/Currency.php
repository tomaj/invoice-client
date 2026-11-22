<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Invoice;

/**
 * Currencies the API accepts.
 */
enum Currency: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case CZK = 'CZK';
    case GBP = 'GBP';
    case HUF = 'HUF';
    case PLN = 'PLN';
    case RUB = 'RUB';
}

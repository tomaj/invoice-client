<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Invoice;

enum DiscountType: string
{
    /** No discount: the discount is not sent to the API. */
    case None = 'none';

    /** The value is a percentage of the price. */
    case Percent = 'percent';

    /** The value is an amount. Only for an item, not for the whole invoice. */
    case Flat = 'flat';
}

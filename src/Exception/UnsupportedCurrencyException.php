<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Exception;

/**
 * Invoice::fromArray() got a currency that is not a Currency case.
 */
final class UnsupportedCurrencyException extends \InvalidArgumentException implements InvoiceClientException {}

<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Exception;

/**
 * The API does not support the discount on this level (a flat discount on the whole invoice).
 */
final class UnsupportedDiscountException extends \InvalidArgumentException implements InvoiceClientException {}

<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Exception;

/**
 * Implemented by every exception the package throws, so callers can catch them all at once.
 */
interface InvoiceClientException extends \Throwable {}

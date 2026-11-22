<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Exception;

/**
 * The request did not get any HTTP response: connection error, timeout, TLS error, too many redirects.
 * An HTTP error response (4xx, 5xx) is not an exception; it is returned as a response with isOk() === false.
 */
final class TransportException extends \RuntimeException implements InvoiceClientException {}

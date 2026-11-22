<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Exception;

/**
 * The invoice cannot be encoded as JSON, for example because a text is not valid UTF-8.
 */
final class SerializationException extends \RuntimeException implements InvoiceClientException {}

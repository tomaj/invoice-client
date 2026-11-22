<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Exception;

/**
 * A value given to the client is not valid: a non-numeric price, an unknown discount type, a target without http(s)://, ...
 */
final class InvalidValueException extends \InvalidArgumentException implements InvoiceClientException {}

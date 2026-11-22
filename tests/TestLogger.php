<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test;

use Psr\Log\AbstractLogger;

/**
 * Collects log records (psr/log 2 and 3 have no test logger).
 */
final class TestLogger extends AbstractLogger
{
    /** @var list<array{mixed, string, array<mixed>}> */
    public array $records = [];

    /**
     * @param array<mixed> $context
     */
    #[\Override]
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->records[] = [$level, (string) $message, $context];
    }
}

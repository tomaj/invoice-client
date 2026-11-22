<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test;

use PHPUnit\Framework\TestCase;
use Tomaj\InvoiceClient\Exception\InvoiceClientException;

final class ExceptionsTest extends TestCase
{
    public function testEveryExceptionClassImplementsThePackageException(): void
    {
        $files = glob(__DIR__ . '/../src/Exception/*.php');
        self::assertNotFalse($files);
        $classes = 0;
        foreach ($files as $file) {
            $class = 'Tomaj\\InvoiceClient\\Exception\\' . basename($file, '.php');
            if ($class === InvoiceClientException::class) {
                continue;
            }
            self::assertTrue(is_subclass_of($class, InvoiceClientException::class), $class);
            ++$classes;
        }
        self::assertSame(5, $classes);
    }
}

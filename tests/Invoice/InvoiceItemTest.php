<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test\Invoice;

use PHPUnit\Framework\TestCase;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;

final class InvoiceItemTest extends TestCase
{
    public function testCreateInvoiceItem(): void
    {
        $item = InvoiceItem::fromArray([
            'quantity' => 10,
            'vat' => 20,
            'price_item' => 4.2,
            'price' => 42,
            'price_total' => 50.4,
        ]);
        self::assertEquals(10, $item->getQuantity());
        self::assertEquals(20, $item->getVat());
        self::assertEquals(4.2, $item->getPriceItem());
        self::assertEquals(42, $item->getPrice());
        self::assertEquals(50.4, $item->getPriceTotal());
    }
}

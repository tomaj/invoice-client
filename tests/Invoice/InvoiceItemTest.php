<?php

namespace Invoice;

use PHPUnit_Framework_TestCase;

class InvoiceItemTest extends PHPUnit_Framework_TestCase
{
    public function testCreateInvoiceItem()
    {
        $item = InvoiceItem::fromArray([
            'quantity' => 10,
            'vat' => 20,
            'price_item' => 4.2,
            'price' => 42,
            'price_total' => 50.4,
        ]);
        $this->assertEquals(10, $item->getQuantity());
        $this->assertEquals(20, $item->getVat());
        $this->assertEquals(4.2, $item->getPriceItem());
        $this->assertEquals(42, $item->getPrice());
        $this->assertEquals(50.4, $item->getPriceTotal());
    }
}

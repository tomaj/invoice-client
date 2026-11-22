<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test\Invoice;

use PHPUnit\Framework\TestCase;
use Tomaj\InvoiceClient\Exception\UnsupportedCurrencyException;
use Tomaj\InvoiceClient\Invoice\Currency;
use Tomaj\InvoiceClient\Invoice\Invoice;

final class InvoiceTest extends TestCase
{
    public function testCreateInvoice(): void
    {
        $invoice = Invoice::fromArray([
            'number' => '20150001',
            'name' => 'Invoice 1',
            'id' => 'asfd09iu23oiht3209itgh4eio3ht',
            'created' => '14.5.2015',
            'delivered' => '13.5.2015',
            'due' => '29.5.2015',
            'status' => 'paid',
            'currency' => Currency::CZK,
            'shipping_address' => [
                'email' => 'asfsaf@asdsad.sk',
            ],
            'client' => [
                'name' => 'Albert Albert',
                'address' => [
                    'tel' => '124321 32 32',
                ],
            ],
            'items' => [
                [
                    'quantity' => 10,
                    'price_item' => 14,
                ],
                [
                    'quantity' => 4,
                    'price_item' => 2,
                ],
            ],
        ]);

        self::assertEquals('20150001', $invoice->getNumber());
        self::assertSame('Invoice 1', $invoice->getName());
        self::assertEquals('asfd09iu23oiht3209itgh4eio3ht', $invoice->getId());
        self::assertSame('14.5.2015', $invoice->getCreated());
        self::assertSame('13.5.2015', $invoice->getDelivered());
        self::assertSame('29.5.2015', $invoice->getDue());
        self::assertSame('paid', $invoice->getStatus());
        self::assertSame(Currency::CZK, $invoice->getCurrency());

        self::assertSame('asfsaf@asdsad.sk', $invoice->getShippingAddress()?->getEmail());

        $client = $invoice->getClient();
        self::assertNotNull($client);
        self::assertSame('Albert Albert', $client->getName());
        self::assertEquals('124321 32 32', $client->getAddress()?->getTel());

        $items = $invoice->getItems();
        self::assertEquals(10, $items[0]->getQuantity());
        self::assertEquals(14, $items[0]->getPriceItem());
        self::assertEquals(4, $items[1]->getQuantity());
        self::assertEquals(2, $items[1]->getPriceItem());
    }

    public function testUnsupportedCurrency(): void
    {
        $this->expectException(UnsupportedCurrencyException::class);
        Invoice::fromArray([
            'id' => 'asfd09iu23oiht3209itgh4eio3ht',
            'created' => '14.5.2015',
            'delivered' => '13.5.2015',
            'due' => '29.5.2015',
            'currency' => 'asdq3r3',
        ]);
    }
}

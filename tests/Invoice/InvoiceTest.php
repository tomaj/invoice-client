<?php

namespace Invoice;

use PHPUnit_Framework_TestCase;

class InvoiceTest extends PHPUnit_Framework_TestCase
{
    public function testCreateInvoice()
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
                ]
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
            ]
        ]);

        $this->assertEquals('20150001', $invoice->getNumber());
        $this->assertEquals('Invoice 1', $invoice->getName());
        $this->assertEquals('asfd09iu23oiht3209itgh4eio3ht', $invoice->getId());
        $this->assertEquals('14.5.2015', $invoice->getCreated());
        $this->assertEquals('13.5.2015', $invoice->getDelivered());
        $this->assertEquals('29.5.2015', $invoice->getDue());
        $this->assertEquals('paid', $invoice->getStatus());
        $this->assertEquals('CZK', $invoice->getCurrency());

        $this->assertEquals('asfsaf@asdsad.sk', $invoice->getShippingAddress()->getEmail());

        $client = $invoice->getClient();
        $this->assertEquals('Albert Albert', $client->getName());
        $this->assertEquals('124321 32 32', $client->getAddress()->getTel());

        $items = $invoice->getItems();
        $this->assertEquals(10, $items[0]->getQuantity());
        $this->assertEquals(14, $items[0]->getPriceItem());
        $this->assertEquals(4, $items[1]->getQuantity());
        $this->assertEquals(2, $items[1]->getPriceItem());
    }

    /**
     * @expectedException \Invoice\Exception\UnsupportedCurrencyException
     */
    public function testUnsupportedCurrency()
    {
        Invoice::fromArray([
            'id' => 'asfd09iu23oiht3209itgh4eio3ht',
            'created' => '14.5.2015',
            'delivered' => '13.5.2015',
            'due' => '29.5.2015',
            'currency' => 'asdq3r3'
        ]);
    }
}

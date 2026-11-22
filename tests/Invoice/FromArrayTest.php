<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test\Invoice;

use PHPUnit\Framework\TestCase;
use Tomaj\InvoiceClient\Exception\UnsupportedCurrencyException;
use Tomaj\InvoiceClient\Invoice\Currency;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\Issuer;

final class FromArrayTest extends TestCase
{
    public function testEveryField(): void
    {
        $invoice = Invoice::fromArray([
            'number' => '2024001', 'name' => 'Invoice', 'id' => 77, 'created' => '2024-01-02', 'delivered' => '2024-01-03',
            'due' => '2024-01-17', 'status' => 'paid', 'description' => 'Popis', 'variable_symbol' => 2024001,
            'constant_symbol' => '0308', 'price' => '100.50', 'price_total' => 120.6, 'currency' => Currency::USD,
            'shipping_address' => [
                'street' => 'Hlavná 1', 'street2' => 'byt 2', 'zip' => 83104, 'city' => 'Bratislava', 'country' => 'SK',
                'state' => 'BA', 'email' => 'a@b.sk', 'tel' => '+421900', 'fax' => 2.5,
            ],
            'client' => [
                'name' => 'Ján', 'company' => 'Firma', 'identifier' => 12345678, 'vat_number' => 'SK2020', 'tax_number' => 2020,
                'address' => ['city' => 'Košice'],
            ],
            'issuer' => ['name' => 'ignored'],
            'items' => [['description' => 'Item', 'quantity' => 1.5, 'vat' => '20', 'price_item' => 2, 'price' => 3, 'price_total' => 3.6], 'not an item'],
        ]);

        self::assertSame('2024001', $invoice->getNumber());
        self::assertSame('Invoice', $invoice->getName());
        self::assertSame(77, $invoice->getId());
        self::assertSame('2024-01-02', $invoice->getCreated());
        self::assertSame('2024-01-03', $invoice->getDelivered());
        self::assertSame('2024-01-17', $invoice->getDue());
        self::assertSame('paid', $invoice->getStatus());
        self::assertSame('Popis', $invoice->getDescription());
        self::assertSame(2024001, $invoice->getVariableSymbol());
        self::assertSame('0308', $invoice->getConstantSymbol());
        self::assertSame('100.50', $invoice->getPrice());
        self::assertSame(120.6, $invoice->getPriceTotal());
        self::assertSame(Currency::USD, $invoice->getCurrency());
        self::assertInstanceOf(Issuer::class, $invoice->getIssuer());

        $address = $invoice->getShippingAddress();
        self::assertNotNull($address);
        self::assertSame(['Hlavná 1', 'byt 2', 83104, 'Bratislava', 'SK', 'BA', 'a@b.sk', '+421900', '2.5'], [
            $address->getStreet(), $address->getStreet2(), $address->getZip(), $address->getCity(), $address->getCountry(),
            $address->getState(), $address->getEmail(), $address->getTel(), $address->getFax(),
        ]);

        $client = $invoice->getClient();
        self::assertNotNull($client);
        self::assertSame(['Ján', 'Firma', 12345678, 'SK2020', 2020, 'Košice'], [
            $client->getName(), $client->getCompany(), $client->getIdentifier(), $client->getVatNumber(), $client->getTaxNumber(),
            $client->getAddress()?->getCity(),
        ]);

        self::assertCount(1, $invoice->getItems());
        $item = $invoice->getItems()[0];
        self::assertSame(['Item', 1.5, '20', 2, 3, 3.6], [
            $item->getDescription(), $item->getQuantity(), $item->getVat(), $item->getPriceItem(), $item->getPrice(), $item->getPriceTotal(),
        ]);
    }

    public function testValuesOfUnexpectedTypesAreIgnored(): void
    {
        $invoice = Invoice::fromArray([
            'number' => true, 'name' => ['x'], 'price' => 'abc', 'price_total' => null, 'client' => 'not an array',
            'items' => 'not a list', 'shipping_address' => ['zip' => false, 'city' => 5, 'tel' => [1]],
        ]);

        self::assertNull($invoice->getNumber());
        self::assertNull($invoice->getName());
        self::assertNull($invoice->getPrice());
        self::assertNull($invoice->getPriceTotal());
        self::assertNull($invoice->getClient());
        self::assertSame([], $invoice->getItems());
        $address = $invoice->getShippingAddress();
        self::assertNotNull($address);
        self::assertNull($address->getZip());
        self::assertSame('5', $address->getCity());
        self::assertNull($address->getTel());
    }

    public function testCurrencyMustBeKnown(): void
    {
        foreach (['eur' => "'eur'", 5 => "'5'"] as $currency => $shown) {
            try {
                Invoice::fromArray(['currency' => $currency]);
                self::fail('UnsupportedCurrencyException expected');
            } catch (UnsupportedCurrencyException $e) {
                self::assertSame("Unsupported currency {$shown}", $e->getMessage());
            }
        }

        $this->expectException(UnsupportedCurrencyException::class);
        $this->expectExceptionCode(0);
        Invoice::fromArray(['currency' => ['EUR']]);
    }
}

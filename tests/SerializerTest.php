<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test;

use PHPUnit\Framework\TestCase;
use Tomaj\InvoiceClient\Exception\InvoiceClientException;
use Tomaj\InvoiceClient\Exception\SerializationException;
use Tomaj\InvoiceClient\Invoice\Address;
use Tomaj\InvoiceClient\Invoice\Client;
use Tomaj\InvoiceClient\Invoice\Currency;
use Tomaj\InvoiceClient\Invoice\Discount;
use Tomaj\InvoiceClient\Invoice\DiscountType;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
use Tomaj\InvoiceClient\Serializer;

final class SerializerTest extends TestCase
{
    public function testFullInvoice(): void
    {
        $invoice = new Invoice()
            ->setCurrency(Currency::EUR)
            ->setShippingAddress(new Address()->setStreet('Hlavná 1')->setZip('83104'))
            ->setClient(new Client()->setName('Ján')->setAddress(new Address()->setCity('Košice')->setTel(421900)))
            ->setDiscount(new Discount(DiscountType::Percent, 5))
            ->addItem(new InvoiceItem()->setDescription('No discount')->setDiscount(new Discount(DiscountType::None, 3)))
            ->addItem(new InvoiceItem()->setDescription('Flat')->setDiscount(new Discount(DiscountType::Flat, '1.50')));

        $data = json_decode(new Serializer()->encodeInvoice($invoice), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($data);
        self::assertSame([
            'invoice' => [
                'id' => null, 'number' => null, 'name' => null, 'created' => null, 'delivered' => null, 'due' => null,
                'status' => null, 'variable_symbol' => null, 'constant_symbol' => null, 'description' => null,
                'items' => [
                    ['vat' => null, 'quantity' => null, 'description' => 'No discount', 'price_item' => null, 'price' => null, 'price_total' => null],
                    ['vat' => null, 'quantity' => null, 'description' => 'Flat', 'price_item' => null, 'price' => null, 'price_total' => null, 'discount' => ['type' => 'flat', 'value' => '1.50']],
                ],
                'price' => null, 'price_total' => null, 'currency' => 'EUR',
                'client' => [
                    'name' => 'Ján', 'company' => null, 'identifier' => null, 'vat_number' => null, 'tax_number' => null,
                    'address' => ['street' => null, 'street2' => null, 'zip' => null, 'city' => 'Košice', 'country' => null, 'state' => null, 'email' => null, 'tel' => 421900, 'fax' => null],
                ],
                'shipping_address' => ['street' => 'Hlavná 1', 'street2' => null, 'zip' => '83104', 'city' => null, 'country' => null, 'state' => null, 'email' => null, 'tel' => null, 'fax' => null],
                'discount' => ['type' => 'percent', 'value' => 5],
            ],
        ], $data);
    }

    public function testClientWithoutAddress(): void
    {
        $data = json_decode(new Serializer()->encodeInvoice(new Invoice()->setClient(new Client()->setName('x'))), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($data);
        self::assertIsArray($data['invoice']);
        self::assertIsArray($data['invoice']['client']);
        self::assertArrayNotHasKey('address', $data['invoice']['client']);
        self::assertArrayNotHasKey('shipping_address', $data['invoice']);
        self::assertArrayNotHasKey('discount', $data['invoice']);
    }

    public function testInvalidUtf8ThrowsAPackageException(): void
    {
        try {
            new Serializer()->encodeInvoice(new Invoice()->setName("Invalid \xB1\x31"));
            self::fail('SerializationException expected');
        } catch (InvoiceClientException $e) {
            self::assertInstanceOf(SerializationException::class, $e);
            self::assertInstanceOf(\JsonException::class, $e->getPrevious());
            self::assertSame('Cannot encode the invoice as JSON: Malformed UTF-8 characters, possibly incorrectly encoded', $e->getMessage());
            self::assertSame(0, $e->getCode());
        }
    }
}

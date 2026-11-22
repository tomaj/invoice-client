<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test\Invoice;

use PHPUnit\Framework\TestCase;
use Tomaj\InvoiceClient\Invoice\Client;

final class ClientTest extends TestCase
{
    public function testCreateClient(): void
    {
        $item = Client::fromArray([
            'name' => 'test name',
            'company' => 'company s.r.o',
            'vat_number' => 'SK214098724',
            'tax_number' => '214098724',
            'address' => [
                'street' => 'Local Street 245',
                'street2' => '124/2142',
                'zip' => '23021',
                'city' => 'New York',
                'country' => 'USA',
                'tel' => '+420 123 123 421',
                'fax' => '325324324',
                'email' => 'sdsada@asdsads.com',
            ],
        ]);
        self::assertSame('test name', $item->getName());
        self::assertSame('company s.r.o', $item->getCompany());
        self::assertEquals('SK214098724', $item->getVatNumber());
        self::assertEquals('214098724', $item->getTaxNumber());

        $address = $item->getAddress();
        self::assertNotNull($address);
        self::assertSame('Local Street 245', $address->getStreet());
        self::assertSame('124/2142', $address->getStreet2());
        self::assertEquals('23021', $address->getZip());
        self::assertSame('New York', $address->getCity());
        self::assertSame('USA', $address->getCountry());
        self::assertEquals('+420 123 123 421', $address->getTel());
        self::assertEquals('325324324', $address->getFax());
        self::assertSame('sdsada@asdsads.com', $address->getEmail());
    }
}

<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Test\Invoice;

use PHPUnit\Framework\TestCase;
use Tomaj\InvoiceClient\Invoice\Address;
use Tomaj\InvoiceClient\Invoice\Client;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
use Tomaj\InvoiceClient\Invoice\Issuer;

final class SettersTest extends TestCase
{
    public function testAddress(): void
    {
        $address = new Address()
            ->setStreet('Street')->setStreet2('Street 2')->setZip(83104)->setCity('City')->setCountry('SK')
            ->setState('BA')->setEmail('a@b.sk')->setTel('+421')->setFax(2);

        self::assertSame(['Street', 'Street 2', 83104, 'City', 'SK', 'BA', 'a@b.sk', '+421', 2], [
            $address->getStreet(), $address->getStreet2(), $address->getZip(), $address->getCity(), $address->getCountry(),
            $address->getState(), $address->getEmail(), $address->getTel(), $address->getFax(),
        ]);
    }

    public function testClient(): void
    {
        $address = new Address();
        $client = new Client()->setName('Name')->setCompany('Company')->setIdentifier('123')->setVatNumber(2020)->setTaxNumber('20')->setAddress($address);

        self::assertSame(['Name', 'Company', '123', 2020, '20'], [
            $client->getName(), $client->getCompany(), $client->getIdentifier(), $client->getVatNumber(), $client->getTaxNumber(),
        ]);
        self::assertSame($address, $client->getAddress());
    }

    public function testInvoice(): void
    {
        $issuer = new Issuer();
        $invoice = new Invoice()
            ->setId('id')->setCreated('2024-01-01')->setDelivered('2024-01-02')->setDue('2024-01-16')->setStatus('paid')
            ->setIssuer($issuer);

        self::assertSame(['id', '2024-01-01', '2024-01-02', '2024-01-16', 'paid'], [
            $invoice->getId(), $invoice->getCreated(), $invoice->getDelivered(), $invoice->getDue(), $invoice->getStatus(),
        ]);
        self::assertSame($issuer, $invoice->getIssuer());
    }

    public function testInvoiceItem(): void
    {
        $item = new InvoiceItem()->setPriceTotal('12.00');

        self::assertSame('12.00', $item->getPriceTotal());
    }
}

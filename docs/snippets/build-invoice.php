<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Tomaj\InvoiceClient\Invoice\Address;
use Tomaj\InvoiceClient\Invoice\Client;
use Tomaj\InvoiceClient\Invoice\Currency;
use Tomaj\InvoiceClient\Invoice\Discount;
use Tomaj\InvoiceClient\Invoice\DiscountType;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
use Tomaj\InvoiceClient\Serializer;

$invoice = new Invoice()
    ->setNumber('2024001')
    ->setName('Invoice 2024001')
    ->setCreated('2024-11-21')
    ->setDue('2024-12-05')
    ->setVariableSymbol(2024001)
    ->setCurrency(Currency::EUR)
    ->setClient(
        new Client()
            ->setCompany('Example s.r.o.')
            ->setIdentifier('12345678')
            ->setVatNumber('SK2020123456')
            ->setAddress(new Address()->setStreet('Hlavná 1')->setZip('83104')->setCity('Bratislava')->setCountry('SK')),
    )
    ->addItem(new InvoiceItem()->setDescription('Consulting')->setQuantity(10)->setPrice('40.00')->setVat(20))
    ->addItem(
        new InvoiceItem()
            ->setDescription('Licence')
            ->setQuantity(1)
            ->setPrice(100)
            ->setVat(20)
            ->setDiscount(new Discount(DiscountType::Flat, 15)),
    )
    ->setDiscount(new Discount(DiscountType::Percent, 5));

// the same invoice from an array, e.g. from your database
$fromArray = Invoice::fromArray([
    'number' => '2024001',
    'currency' => 'EUR',
    'items' => [['description' => 'Consulting', 'quantity' => 10, 'price' => '40.00', 'vat' => 20]],
]);

// what createInvoice() sends: values are kept as given ("40.00" stays a string, 10 an integer)
$json = json_decode(new Serializer()->encodeInvoice($fromArray), true, 512, JSON_THROW_ON_ERROR);
echo json_encode($json, JSON_PRETTY_PRINT), "\n";

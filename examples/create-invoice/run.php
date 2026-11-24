<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Tomaj\InvoiceClient\Invoice\Address;
use Tomaj\InvoiceClient\Invoice\Client;
use Tomaj\InvoiceClient\Invoice\Currency;
use Tomaj\InvoiceClient\Invoice\Discount;
use Tomaj\InvoiceClient\Invoice\DiscountType;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
use Tomaj\InvoiceClient\InvoiceApi;

// In production: new InvoiceApi((string) getenv('CHCEMFAKTURU_API_TOKEN'));
// Here the fake API started by e2e.sh, which accepts the token "test-token".
$api = new InvoiceApi('test-token', target: getenv('FAKE_API_URL') ?: 'http://127.0.0.1:8701');

$client = new Client()
    ->setCompany('Example s.r.o.')
    ->setName('Jana Nováková')
    ->setIdentifier('12345678')       // IČO
    ->setTaxNumber('2020123456')      // DIČ
    ->setVatNumber('SK2020123456')    // IČ DPH
    ->setAddress(
        new Address()
            ->setStreet('Hlavná 1')
            ->setZip('83104')
            ->setCity('Bratislava')
            ->setCountry('Slovakia')
            ->setEmail('billing@example.sk'),
    );

$invoice = new Invoice()
    ->setNumber('2026001')
    ->setName('Invoice 2026001')
    ->setCreated('2026-11-24')
    ->setDelivered('2026-11-24')
    ->setDue('2026-12-08')
    ->setVariableSymbol('2026001')
    ->setConstantSymbol('0308')       // a string keeps the leading zero
    ->setCurrency(Currency::EUR)
    ->setDescription('Thank you for your order.')
    ->setClient($client)
    ->setShippingAddress(new Address()->setStreet('Skladová 5')->setZip('82105')->setCity('Bratislava')->setCountry('Slovakia'))
    ->addItem(new InvoiceItem()->setDescription('Consulting (hours)')->setQuantity(10)->setPriceItem('40.00')->setVat(20))
    ->addItem(
        new InvoiceItem()
            ->setDescription('Annual licence')
            ->setQuantity(1)
            ->setPriceItem(120)
            ->setVat(20)
            ->setDiscount(new Discount(DiscountType::Flat, '20.00')),   // 20.00 off this item
    )
    ->setDiscount(new Discount(DiscountType::Percent, 5));             // 5 % off the whole invoice

$response = $api->createInvoice($invoice);

if (!$response->isOk()) {
    echo 'Error (HTTP ', $response->getStatusCode(), '): ', $response->getErrorMessage(), "\n";
    exit(1);
}

echo 'HTTP status: ', $response->getStatusCode(), "\n";
echo 'Invoice id:  ', $response->getId(), "\n";
echo 'HTML:        ', $response->getHtmlUrl(), "\n";
echo 'PDF:         ', $response->getDownloadUrl(), "\n";

// the same links, built by the client from the id without a request
$id = (string) $response->getId();
echo 'htmlUrl():        ', $api->htmlUrl($id), "\n";
echo 'pdfUrl():         ', $api->pdfUrl($id), "\n";
echo 'pdfDownloadUrl(): ', $api->pdfDownloadUrl($id), "\n";

// what the API received, as the fake API stored it
$stored = $api->getInvoice($id)->getData()['invoice'];
echo "\nStored by the API:\n", json_encode($stored, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "\n";

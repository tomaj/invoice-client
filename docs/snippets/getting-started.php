<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Tomaj\InvoiceClient\Invoice\Currency;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
use Tomaj\InvoiceClient\InvoiceApi;

$api = new InvoiceApi((string) getenv('CHCEMFAKTURU_API_TOKEN'));

$invoice = new Invoice()
    ->setName('Invoice 2024001')
    ->setNumber('2024001')
    ->setCurrency(Currency::EUR)
    ->addItem(new InvoiceItem()->setDescription('Consulting')->setQuantity(10)->setPrice('40.00')->setVat(20));

$response = $api->createInvoice($invoice);

if ($response->isOk()) {
    echo 'Invoice ', $response->getId(), "\n";
    echo 'PDF: ', $response->getDownloadUrl(), "\n";
    echo 'HTML: ', $response->getHtmlUrl(), "\n";
} else {
    echo 'Error (HTTP ', $response->getStatusCode(), '): ', $response->getErrorMessage(), "\n";
}

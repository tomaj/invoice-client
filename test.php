<?php

use Invoice\InvoiceApi;
use Invoice\Invoice;
use Invoice\InvoiceItem;
use Invoice\Discount;

require 'vendor/autoload.php';

$baseUrl = 'http://invoice.localhost.sk';
//$baseUrl = 'https://chcemfakturu.sk';

// test 1

$invoiceApi = new InvoiceApi('asdsad', [
   'target' => $baseUrl,
]);
$invoice = new Invoice();
$invoice->setName('nazov 1')
    ->setPrice(12.3)
    ->setNumber('123213');
//$result = $invoiceApi->createInvoice($invoice);

//if (!$result->isOk()) {
//    echo "[Test1] Unknown API key ... OK\n";
//} else {
//    echo "[Test1] Unknown API key ... Fail\n";
//}

// test 2

if (!isset($argv[1])) {
    die('add token as argumen!!!');
}
$token = $argv[1];
echo "Trying token: $token\n";

$invoiceApi = new InvoiceApi($token, [
    'target' => $baseUrl,
]);
$invoice = new Invoice();
$invoice->setName('nazov 1')
    ->addItem((new InvoiceItem())
        ->setQuantity(10)
        ->setPriceItem(3.2)
        ->setVat(20)
        ->setDescription('Item 1')
        ->setDiscount(new Discount('percent', 20)))
    ->addItem((new InvoiceItem())
        ->setQuantity(1)
        ->setPriceItem(45)
        ->setDescription('Item 2')
        ->setVat(10)
        ->setDiscount(new Discount('flat', 10)))
    ->setDiscount(new Discount('percent', 5))
;
$result = $invoiceApi->createInvoice($invoice);
print_r($result);
if ($result->isOk()) {
    echo "[Test2] Create invoice ... OK\n";
    echo  "  - html: {$result->getHtmlUrl()}\n";
    echo  "  - pdf: {$result->getDownloadUrl()}\n";
} else {
    echo "[Test2] Create invoice ... Fail\n";
    echo "  -> {$result->getErrorMessage()}\n";
}

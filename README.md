Invoice client
==============

In development mode.



[![CI](https://github.com/tomaj/invoice-client/actions/workflows/ci.yml/badge.svg)](https://github.com/tomaj/invoice-client/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/tomaj/invoice-client/v/stable)](https://packagist.org/packages/tomaj/invoice-client)


Requirements
------------

PHP 8.3 or newer.

Basic usage
-----------

```php

use Invoice\InvoiceApi;
use Invoice\Invoice;
use Invoice\InvoiceItem;

$invoiceApi = new InvoiceApi('*APIKEY*');

$invoiceItem = new InvoiceItem();
$invoiceItem
    ->setQuantity(10)
    ->setPrice(2.4)
    ->setVat(20);

$invoice = new Invoice();
$invoice
    ->setName('Invoice 12323')
    ->setDescription('invoice description...')
    ->addItem($invoiceItem);
    // there are other method to set invoice properties... 

$result = $invoiceApi->createInvoice($invoice);

if ($result->isOK()) {
    echo "Download pdf: " . $result->getDownloadUrl() . "\n";
    echo "Online HTML version:" . $result->getHtmlUrl() . "\n";
} else {
    echo "Error: " . $result->getErrorMessage() . "\n";
}

```

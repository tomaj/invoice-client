Invoice client
==============

In development mode.



[![Build Status](https://travis-ci.org/tomaj/invoice-client.svg)](http://travis-ci.org/tomaj/invoice-client)
[![Code Climate](https://codeclimate.com/github/tomaj/invoice-client/badges/gpa.svg)](https://codeclimate.com/github/tomaj/invoice-client)
[![Test Coverage](https://codeclimate.com/github/tomaj/invoice-client/badges/coverage.svg)](https://codeclimate.com/github/tomaj/invoice-client/coverage)
[![Dependency Status](https://www.versioneye.com/user/projects/558c6fbc653232001e000687/badge.svg?style=flat)](https://www.versioneye.com/user/projects/558c6fbc653232001e000687)

[![Latest Stable Version](https://poser.pugx.org/tomaj/invoice-client/v/stable)](https://packagist.org/packages/tomaj/csv-processor)


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

Invoice client
==============

In development mode.


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

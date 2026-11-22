<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Transport\Result;
use Tomaj\InvoiceClient\Transport\TestTransport;

// in your test: queue the API responses, run your code, check what it sent
$transport = new TestTransport()
    ->addResponse(new Result(200, '{"status":"ok","id":"inv-1","html_url":"https://example.test/html/inv-1","download_pdf_url":"https://example.test/pdf-download/inv-1"}'))
    ->addResponse(new Result(422, '{"status":"error","message":"Invalid VAT number"}'));

$api = new InvoiceApi('test-token', transport: $transport);

$first = $api->createInvoice(new Invoice()->setName('First'));
$second = $api->createInvoice(new Invoice()->setName('Second'));

echo $first->isOk() ? 'created ' . $first->getId() : 'failed', "\n";
echo $second->isOk() ? 'created' : 'failed: ' . $second->getErrorMessage(), "\n";

foreach ($transport->getRequests() as $request) {
    echo $request['method']->value, ' ', $request['url'], "\n";
}

<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Response\ResponseInterface;

$api = new InvoiceApi('test-token', target: getenv('FAKE_API_URL') ?: 'http://127.0.0.1:8702');

function show(string $call, ResponseInterface $response): void
{
    printf(
        "%-30s HTTP %d  %s\n",
        $call,
        $response->getStatusCode(),
        $response->isOk() ? 'ok' : 'error: ' . $response->getErrorMessage(),
    );
}

// 1. An order row from your database, as an array with the API field names
$order = [
    'number' => 2026002,
    'name' => 'Invoice 2026002',
    'created' => '2026-11-24',
    'due' => '2026-12-08',
    'variable_symbol' => 2026002,
    'currency' => 'EUR',
    'client' => [
        'name' => 'Peter Horváth',
        'address' => ['street' => 'Dlhá 12', 'zip' => '04001', 'city' => 'Košice', 'country' => 'Slovakia'],
    ],
    'items' => [
        ['description' => 'Monthly subscription', 'quantity' => 1, 'price_item' => '9.90', 'vat' => 20],
        ['description' => 'Setup fee', 'quantity' => 1, 'price_item' => '25.00', 'vat' => 20, 'discount' => ['type' => 'percent', 'value' => 50]],
    ],
];

$created = $api->createInvoice(Invoice::fromArray($order));
show('createInvoice()', $created);
$id = (string) $created->getId();
echo "  id: {$id}\n";

// 2. Fetch it. getData() is the decoded JSON body, exactly as the API sent it.
$detail = $api->getInvoice($id);
show("getInvoice('{$id}')", $detail);
$invoice = $detail->getData()['invoice'] ?? [];
if (is_array($invoice) && is_array($invoice['items'] ?? null) && is_array($invoice['client'] ?? null)) {
    echo '  name: ', $invoice['name'] ?? '', "\n";
    echo '  client: ', $invoice['client']['name'] ?? '', "\n";
    echo '  items: ', count($invoice['items']), "\n";
}

// 3. Links for your customer: no request is sent
echo '  html: ', $api->htmlUrl($id), "\n";
echo '  pdf download: ', $api->pdfDownloadUrl($id), "\n";

// 4. Delete it
show("deleteInvoice('{$id}')", $api->deleteInvoice($id));

// 5. It is gone: the API answers 404, which is a response, not an exception
show("getInvoice('{$id}')", $api->getInvoice($id));
show("deleteInvoice('{$id}')", $api->deleteInvoice($id));

// Ids are URL-encoded, so an id with a slash stays one path segment
show("getInvoice('2026/002')", $api->getInvoice('2026/002'));
echo '  html: ', $api->htmlUrl('2026/002'), "\n";

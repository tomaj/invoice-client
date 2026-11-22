<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Tomaj\InvoiceClient\Exception\InvoiceClientException;
use Tomaj\InvoiceClient\Exception\TransportException;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\InvoiceApi;

$api = new InvoiceApi((string) getenv('CHCEMFAKTURU_API_TOKEN'));

try {
    $response = $api->createInvoice(new Invoice()->setName('Invoice 2024001'));
} catch (TransportException $e) {
    // no HTTP response at all: DNS, connection refused, timeout, TLS error
    error_log('Invoice API unreachable: ' . $e->getMessage());
    exit(1);
} catch (InvoiceClientException $e) {
    // any other error of the package, here SerializationException: a text of the invoice is not valid UTF-8
    error_log($e->getMessage());
    exit(1);
}

if (!$response->isOk()) {
    // the API answered with an error: wrong token (401), validation error (422), server error (5xx)
    error_log(sprintf('Invoice API error %d: %s', $response->getStatusCode(), $response->getErrorMessage()));
    // the raw body, e.g. for a support ticket
    error_log($response->getBody());
    exit(1);
}

echo 'Created invoice ', $response->getId(), "\n";

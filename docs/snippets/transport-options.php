<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client;
use Psr\Log\NullLogger;
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Transport\GuzzleTransport;

// your own Guzzle client: timeouts, proxy, middleware
$transport = new GuzzleTransport(new Client([
    'connect_timeout' => 3,
    'timeout' => 20,
    'headers' => ['User-Agent' => 'MyShop/1.0'],
]));

// any PSR-3 logger, e.g. Monolog; it gets the method, URL, status code and API errors, never the token or the invoice
$logger = new NullLogger();

$api = new InvoiceApi(
    apiToken: (string) getenv('CHCEMFAKTURU_API_TOKEN'),
    target: InvoiceApi::DEFAULT_TARGET,
    transport: $transport,
    logger: $logger,
);

echo $api->pdfUrl('inv-1'), "\n";

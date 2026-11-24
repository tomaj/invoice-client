<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Tomaj\InvoiceClient\Exception\InvoiceClientException;
use Tomaj\InvoiceClient\Exception\TransportException;
use Tomaj\InvoiceClient\Invoice\Discount;
use Tomaj\InvoiceClient\Invoice\DiscountType;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Response\ResponseInterface;
use Tomaj\InvoiceClient\Transport\NullTransport;

$url = getenv('FAKE_API_URL') ?: 'http://127.0.0.1:8703';

function invoice(): Invoice
{
    return new Invoice()
        ->setName('Invoice 2026003')
        ->addItem(new InvoiceItem()->setDescription('Consulting')->setQuantity(1)->setPriceItem(100)->setVat(20));
}

function report(string $case, ResponseInterface $response): void
{
    echo "== {$case}\n";
    echo '   isOk(): ', var_export($response->isOk(), true), "\n";
    echo '   getStatusCode(): ', $response->getStatusCode(), "\n";
    echo '   getErrorMessage(): ', var_export($response->getErrorMessage(), true), "\n";
    echo '   getBody(): ', var_export(trim($response->getBody()), true), "\n";
}

function attempt(string $case, callable $code): void
{
    echo "== {$case}\n";
    try {
        $code();
        echo "   no exception\n";
    } catch (InvoiceClientException $e) {
        // one catch for every exception of the package
        echo '   ', $e::class, "\n";
        // a TransportException message ends with the cURL error, which differs per system: print its start
        echo '   ', $e instanceof TransportException ? strstr($e->getMessage(), ': ', true) : $e->getMessage(), "\n";
        if ($e->getPrevious() !== null) {
            echo '   previous: ', $e->getPrevious()::class, "\n";
        }
    }
}

echo "API errors are responses, check isOk():\n\n";

// A wrong token: the API answers 401 with a JSON error
report('wrong token', new InvoiceApi('wrong-token', target: $url)->createInvoice(invoice()));

// A validation error: 422 with the API's message
report('invoice without items', new InvoiceApi('test-token', target: $url)->createInvoice(new Invoice()->setName('Empty')));

// An unknown id: 404
report('unknown invoice', new InvoiceApi('test-token', target: $url)->getInvoice('inv-9999'));

// A proxy answers with an HTML page: the body is kept, the message says it could not be parsed
report('HTML error page from a proxy', new InvoiceApi('test-token', target: $url . '/outage')->createInvoice(invoice()));

// NullTransport sends nothing: status 0
report('NullTransport', new InvoiceApi('test-token', transport: new NullTransport())->createInvoice(invoice()));

echo "\nExceptions mean no HTTP response or invalid input:\n\n";

// Nothing listens on port 9: no HTTP response at all
attempt('server not reachable', fn () => new InvoiceApi('test-token', target: 'http://127.0.0.1:9')->createInvoice(invoice()));

attempt('target without a scheme', fn () => new InvoiceApi('test-token', target: 'chcemfakturu.sk'));

attempt('non-numeric price', fn () => new InvoiceItem()->setPriceItem('forty'));

attempt('flat discount on the whole invoice', fn () => new Invoice()->setDiscount(new Discount(DiscountType::Flat, 10)));

attempt('unknown discount type', fn () => Discount::fromArray(['type' => 'gift', 'value' => 10]));

attempt('unsupported currency', fn () => Invoice::fromArray(['currency' => 'BTC']));

attempt('text that is not UTF-8', fn () => new InvoiceApi('test-token', target: $url)->createInvoice(invoice()->setDescription("caf\xE9")));

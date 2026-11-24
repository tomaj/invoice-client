<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tomaj\InvoiceClient\Exception\TransportException;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
use Tomaj\InvoiceClient\InvoiceApi;
use Tomaj\InvoiceClient\Transport\GuzzleTransport;

$url = getenv('FAKE_API_URL') ?: 'http://127.0.0.1:8705';

$stack = HandlerStack::create();

// Retry 502, 503 and 504 up to 3 times. Only GET and DELETE are retried: a POST that timed out
// may still have created the invoice, so retrying it could create a duplicate.
$stack->push(Middleware::retry(
    function (int $retries, RequestInterface $request, ?ResponseInterface $response = null): bool {
        $retry = $retries < 3
            && $request->getMethod() !== 'POST'
            && in_array($response?->getStatusCode(), [502, 503, 504], true);
        if ($retry) {
            echo "  attempt ", $retries + 1, ': HTTP ', $response?->getStatusCode(), ", retrying\n";
        }
        return $retry;
    },
    fn (int $retries): int => 100 * $retries,   // delay in milliseconds
));

// An extra header on every request
$stack->push(Middleware::mapRequest(
    fn (RequestInterface $request): RequestInterface => $request->withHeader('User-Agent', 'acme-shop/1.0'),
));

$guzzle = new Client([
    'handler' => $stack,
    'timeout' => 2,           // seconds for the whole request (GuzzleTransport's default client uses 10)
    'connect_timeout' => 1,
]);

$api = new InvoiceApi('test-token', target: $url, transport: new GuzzleTransport($guzzle));
$invoice = new Invoice()
    ->setName('Invoice 2026005')
    ->addItem(new InvoiceItem()->setDescription('Hosting')->setQuantity(12)->setPriceItem('4.90')->setVat(20));
$id = (string) $api->createInvoice($invoice)->getId();
echo "created {$id}\n";

echo "GET through a server that fails twice with 503:\n";
$flaky = new InvoiceApi('test-token', target: $url . '/flaky', transport: new GuzzleTransport($guzzle));
$detail = $flaky->getInvoice($id);
echo '  result: HTTP ', $detail->getStatusCode(), ', ', $detail->isOk() ? 'ok' : $detail->getErrorMessage(), "\n";

echo "GET from a server that needs 3 seconds, with a 2 second timeout:\n";
$slow = new InvoiceApi('test-token', target: $url . '/slow', transport: new GuzzleTransport($guzzle));
try {
    $slow->getInvoice($id);
    echo "  answered\n";
} catch (TransportException $e) {
    echo '  ', $e::class, ' (', $e->getPrevious()::class, ")\n";
}
